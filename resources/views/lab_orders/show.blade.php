@extends('layouts.app', ['title' => 'টেস্ট ও ল্যাব · #' . $record->id])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>টেস্ট ও ল্যাব · #{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            <div class="detailgrid">
                <div><label>রোগী</label><strong>{{ $record->patient?->name ?? '#' . $record->patient_id }}</strong></div>
                <div><label>ভর্তি নম্বর
                        (ঐচ্ছিক)</label><strong>{{ $record->admission_id ? 'ADM-' . $record->admission_id : '' }}</strong>
                </div>
                <div><label>টেস্ট</label><strong>{{ $record->test?->name ?? '#' . $record->test_id }}</strong></div>
                <div><label>অর্ডারের সময়</label><strong>{{ $record->ordered_at?->format('d-m-Y h:i A') }}</strong></div>
            </div>
            <div class="actions noprint">
                <a class="btn" href="{{ route('lab_orders.index') }}">তালিকা</a>
            </div>
        </div>
    </section>
    @include('lab.detail')
@endsection
