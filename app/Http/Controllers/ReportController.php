<?php
namespace App\Http\Controllers;
use App\Models\ {
    Payment, Expense, Invoice, Admission, DoctorLedger
};
use App\Support\Registry;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ReportController extends Controller {
    public function __invoke(Request $request) {
        Registry::authorize('reports');
        $data = $request->validate(['from' => 'nullable|date', 'to' => 'nullable|date|after_or_equal:from']);
        $from = $data['from'] ?? now()->startOfMonth()->toDateString();
        $to = $data['to'] ?? today()->toDateString();
        $payments = Payment::whereBetween(DB::raw('DATE(paid_at)'), [$from, $to])->whereNull('voided_at');
        $income = (float)(clone $payments)->selectRaw("COALESCE(SUM(CASE WHEN type='receipt' THEN amount ELSE -amount END),0) AS amount")->value('amount');
        $expense = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');
        $daily = (clone $payments)->selectRaw("DATE(paid_at) AS day, SUM(CASE WHEN type='receipt' THEN amount ELSE -amount END) AS amount")->groupBy(DB::raw('DATE(paid_at)'))->get();
        $dues = Invoice::with('patient')->where('status', '!=', 'void')->get()->map(fn($invoice) => ['invoice' => $invoice, 'due' => app(BillingService::class)->totals($invoice)['due']])->filter(fn($row) => $row['due'] > 0);
        $sales = DB::table('lab_orders AS o')->join('tests AS t', 't.id', '=', 'o.test_id')->join('invoice_items AS i', 'i.lab_order_id', '=', 'o.id')->whereBetween(DB::raw('DATE(o.ordered_at)'), [$from, $to])->where('o.status', '!=', 'cancelled')->selectRaw('t.name,COUNT(*) AS count,SUM(i.quantity*i.unit_price) AS amount')->groupBy('t.id', 't.name')->get();
        $doctorBalances = DB::table('doctors AS d')->leftJoin('doctor_ledgers AS l', 'l.doctor_id', '=', 'd.id')->selectRaw("d.name,COALESCE(SUM(CASE WHEN l.type='payable' THEN l.amount ELSE -l.amount END),0) AS balance")->groupBy('d.id', 'd.name')->get();
        $admitted = Admission::whereBetween(DB::raw('DATE(admitted_at)'), [$from, $to])->count();
        $discharged = Admission::where('status', 'discharged')->whereBetween(DB::raw('DATE(discharged_at)'), [$from, $to])->count();
        return view('reports.index', compact('from', 'to', 'income', 'expense', 'daily', 'dues', 'sales', 'doctorBalances', 'admitted', 'discharged'));
    }
}
