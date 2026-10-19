@extends('layouts.app', ['title' => 'নার্সিং রেকর্ড · #' . $record->id])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>নার্সিং রেকর্ড · #{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            <div class="detailgrid">
                <div><label>ভর্তি নম্বর</label><strong>ADM-{{ $record->admission_id }} ·
                        {{ $record->admission?->patient?->name }}</strong></div>
                <div><label>পর্যবেক্ষণের সময়</label><strong>{{ $record->recorded_at }}</strong></div>
                <div><label>রক্তচাপ (mmHg)</label><strong>{{ $record->blood_pressure }}</strong></div>
                <div><label>তাপমাত্রা (°F)</label><strong>{{ $record->temperature }}</strong></div>
                <div><label>পালস</label><strong>{{ $record->pulse }}</strong></div>
                <div><label>নার্সিং নোট</label><strong>{{ $record->notes }}</strong></div>
            </div>
            <div class="actions noprint">
                <a class="btn" href="{{ route('nursing_records.index') }}">তালিকা</a>
                @if (\App\Support\Registry::can('nursing_records', true))
                    <a class="btn" href="{{ route('nursing_records.edit', $record) }}">সম্পাদনা</a>
                    <form method="post" action="{{ route('nursing_records.destroy', $record) }}"
                        data-confirm="রেকর্ড মুছবেন?">
                        @csrf @method('DELETE')
                        <button class="btn danger">ডিলেট</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
