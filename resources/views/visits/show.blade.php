@extends('layouts.app', ['title' => 'ডাক্তার ও সিরিয়াল · #' . $record->id])

@section('heading-action')
    <a class="btn primary" href="{{ route('visits.index') }}"><i class="fa-solid fa-eye"></i> তালিকা</a>
@endsection

@section('content')
    <section class="panel">
        <div class="panelbody">
            @php
                $types = ['new' => 'নতুন', 'followup' => 'ফলোআপ'];
                $statuses = ['waiting' => 'অপেক্ষমাণ', 'completed' => 'সম্পন্ন', 'cancelled' => 'বাতিল'];
            @endphp
            <div class="detailgrid">
                <div><label>রোগী</label><strong>{{ $record->patient?->name ?? '#' . $record->patient_id }}</strong></div>
                <div><label>ডাক্তার</label><strong>{{ $record->doctor?->name ?? '#' . $record->doctor_id }}</strong></div>
                <div><label>রেফারেন্স</label><strong>{{ $record->reference?->name }}</strong></div>
                <div><label>তারিখ ও সময়</label><strong>{{ datetime_to_view($record->appointment_at) }}</strong></div>
                <div><label>সিরিয়াল নম্বর</label><strong>{{ $record->serial_no }}</strong></div>
                <div><label>ভিজিট</label><strong>{{ $types[$record->visit_type] ?? $record->visit_type }}</strong></div>
                <div><label>ভিজিট ফি</label><strong>{{ $record->fee }}</strong></div>
                <div><label>স্ট্যাটাস</label><strong>{{ $statuses[$record->status] ?? $record->status }}</strong></div>
                <div><label>নোট</label><strong>{{ $record->notes }}</strong></div>
            </div>
            @if (\App\Support\Registry::can('visits', true))
                <div class="actions noprint">
                    <a class="btn" href="{{ route('visits.edit', $record) }}"><i class="fa-solid fa-pen"></i> সম্পাদনা</a>
                </div>
            @endif
        </div>
    </section>

    <section class="panel actionpanel noprint">
        <div class="actionbar">
            @include('visits.actions')
        </div>
    </section>
@endsection
