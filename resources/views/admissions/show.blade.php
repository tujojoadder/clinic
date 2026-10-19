@extends('layouts.app', ['title' => 'রোগী ভর্তি · #' . $record->id])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>রোগী ভর্তি · #{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            <div class="detailgrid">
                <div><label>রোগী</label><strong>{{ $record->patient?->name ?? '#' . $record->patient_id }}</strong></div>
                <div><label>ডাক্তার</label><strong>{{ $record->doctor?->name ?? '#' . $record->doctor_id }}</strong></div>
                <div><label>ভর্তির তারিখ ও সময়</label><strong>{{ $record->admitted_at }}</strong></div>
                <div><label>ভর্তির কারণ</label><strong>{{ $record->reason }}</strong></div>
            </div>
            <div class="actions noprint">
                <a class="btn" href="{{ route('admissions.index') }}">তালিকা</a>
                @if ($record->status === 'admitted' && \App\Support\Registry::can('admissions', true))
                    <a class="btn" href="{{ route('admissions.edit', $record) }}">সম্পাদনা</a>
                @endif
            </div>
        </div>
    </section>
    @include('admissions.detail')
@endsection
