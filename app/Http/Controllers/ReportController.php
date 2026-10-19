<?php

namespace App\Http\Controllers;

use App\Models\{
    Payment,
    Expense,
    Invoice,
    Admission
};
use App\Support\Registry;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /* /reports → প্রথম রিপোর্টে নিয়ে যায় */
    public function index()
    {
        Registry::authorize('reports');
        return redirect()->route('reports.income');
    }

    /* ---------- আদায় ও ব্যয় ---------- */
    public function income(Request $request)
    {
        [$from, $to] = $this->range($request);

        $income = (float) $this->payments($from, $to)
            ->selectRaw("COALESCE(SUM(CASE WHEN type='receipt' THEN amount ELSE -amount END),0) AS amount")
            ->value('amount');

        $expense = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');

        return view('reports.income', compact('from', 'to', 'income', 'expense'));
    }

    /* ---------- দৈনিক আদায় ---------- */
    public function daily(Request $request)
    {
        [$from, $to] = $this->range($request);

        $daily = $this->payments($from, $to)
            ->selectRaw("DATE(paid_at) AS day, SUM(CASE WHEN type='receipt' THEN amount ELSE -amount END) AS amount")
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->orderBy('day')
            ->get();

        return view('reports.daily', compact('from', 'to', 'daily'));
    }

    /* ---------- বর্তমান বকেয়া (তারিখ ফিল্টার নেই) ---------- */
    public function dues()
    {
        Registry::authorize('reports');

        $dues = Invoice::with('patient')
            ->where('status', '!=', 'void')
            ->get()
            ->map(fn($invoice) => [
                'invoice' => $invoice,
                'due' => app(BillingService::class)->totals($invoice)['due'],
            ])
            ->filter(fn($row) => $row['due'] > 0)
            ->values();

        return view('reports.dues', compact('dues'));
    }

    /* ---------- টেস্ট বিক্রি ---------- */
    public function sales(Request $request)
    {
        [$from, $to] = $this->range($request);

        $sales = DB::table('lab_orders AS o')
            ->join('tests AS t', 't.id', '=', 'o.test_id')
            ->join('invoice_items AS i', 'i.lab_order_id', '=', 'o.id')
            ->whereBetween(DB::raw('DATE(o.ordered_at)'), [$from, $to])
            ->where('o.status', '!=', 'cancelled')
            ->selectRaw('t.name,COUNT(*) AS count,SUM(i.quantity*i.unit_price) AS amount')
            ->groupBy('t.id', 't.name')
            ->get();

        return view('reports.sales', compact('from', 'to', 'sales'));
    }

    /* ---------- ভর্তি ও ডিসচার্জ ---------- */
    public function admissions(Request $request)
    {
        [$from, $to] = $this->range($request, 'reports_admissions');

        $admittedList = DB::table('admissions AS a')
            ->join('patients AS p', 'p.id', '=', 'a.patient_id')
            ->leftJoin('doctors AS d', 'd.id', '=', 'a.doctor_id')
            ->whereBetween(DB::raw('DATE(a.admitted_at)'), [$from, $to])
            ->orderBy('a.admitted_at')
            ->select('a.id', 'p.name AS patient', 'd.name AS doctor', 'a.admitted_at', 'a.status')
            ->get();

        $dischargedList = DB::table('admissions AS a')
            ->join('patients AS p', 'p.id', '=', 'a.patient_id')
            ->leftJoin('doctors AS d', 'd.id', '=', 'a.doctor_id')
            ->where('a.status', 'discharged')
            ->whereBetween(DB::raw('DATE(a.discharged_at)'), [$from, $to])
            ->orderBy('a.discharged_at')
            ->select('a.id', 'p.name AS patient', 'd.name AS doctor', 'a.discharged_at', 'a.discharge_type')
            ->get();

        $admitted = $admittedList->count();
        $discharged = $dischargedList->count();

        return view('reports.admissions', compact('from', 'to', 'admitted', 'discharged', 'admittedList', 'dischargedList'));
    }
    /* ---------- ডাক্তারের প্রাপ্য (তারিখ ফিল্টার নেই) ---------- */
    public function doctors()
    {
        Registry::authorize('reports');

        $doctorBalances = DB::table('doctors AS d')
            ->leftJoin('doctor_ledgers AS l', 'l.doctor_id', '=', 'd.id')
            ->selectRaw("d.name,COALESCE(SUM(CASE WHEN l.type='payable' THEN l.amount ELSE -l.amount END),0) AS balance")
            ->groupBy('d.id', 'd.name')
            ->get();

        return view('reports.doctors', compact('doctorBalances'));
    }

    /* ---------- সবার কাজে লাগা হেল্পার ---------- */

    // অনুমতি যাচাই + from/to তারিখ ঠিক করা
    private function range(Request $request): array
    {
        Registry::authorize('reports');
        // picker এর 15-10-2026 → 2026-10-15
        $request->merge([
            'from' => date_to_db($request->input('from')),
            'to' => date_to_db($request->input('to')),
        ]);
        $data = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        return [
            $data['from'] ?? now()->startOfMonth()->toDateString(),
            $data['to'] ?? today()->toDateString(),
        ];
    }

    // তারিখের মধ্যে বাতিল-না-হওয়া পেমেন্ট
    private function payments(string $from, string $to)
    {
        return Payment::whereBetween(DB::raw('DATE(paid_at)'), [$from, $to])
            ->whereNull('voided_at');
    }
}
