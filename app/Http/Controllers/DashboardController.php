<?php

namespace App\Http\Controllers;

use App\Models\{Patient, Admission, Bed, Visit, Payment, LabOrder};
use App\Support\Registry;

class DashboardController extends Controller
{
    public function __invoke()
    {
        Registry::authorize('dashboard');
        $stats = [
            ['নিবন্ধিত রোগী', Patient::count(), 'রোগীর প্রোফাইল'],
            ['বর্তমানে ভর্তি', Admission::where('status', 'admitted')->count(), 'সক্রিয় ভর্তি'],
            ['খালি বেড', Bed::where('state', 'available')->whereDoesntHave('stays', fn($q) => $q->whereNull('ended_at'))->count(), 'ভর্তির জন্য প্রস্তুত'],
            ['আজকের সিরিয়াল', Visit::whereDate('appointment_at', today())->where('status', '!=', 'cancelled')->count(), 'ডাক্তার ভিজিট'],
        ];
        if (Registry::can('invoices')) {
            $amount = Payment::whereDate('paid_at', today())->whereNull('voided_at')->selectRaw("COALESCE(SUM(CASE WHEN type='receipt' THEN amount ELSE -amount END),0) AS amount")->value('amount');
            $stats[] = ['আজকের আদায় ', \App\Support\Format::money($amount), 'নিট সংগ্রহ'];
        }
        $visits = Registry::can('visits') ? Visit::with('patient', 'doctor')->whereDate('appointment_at', today())->orderBy('appointment_at')->limit(10)->get() : collect();
        $lab = Registry::can('lab_orders') ? LabOrder::selectRaw('status,COUNT(*) AS count')->groupBy('status')->get() : collect();
        $admissions = Registry::can('admissions') ? Admission::with('patient')->where('status', 'admitted')->latest('id')->limit(8)->get() : collect();
        return view('dashboard.index', compact('stats', 'visits', 'lab', 'admissions'));
    }

    public function discharge()
    {
        Registry::authorize('discharge');
        $admissions = Admission::with('patient')->where('status', 'admitted')->latest('id')->get();
        return view('discharge.index', compact('admissions'));
    }
}
