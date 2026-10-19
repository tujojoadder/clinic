@extends('layouts.app', ['title' => 'বিলিং ও পেমেন্ট · #' . $record->id])

@section('heading-action')
    <a class="btn primary" href="{{ route('invoices.index') }}"><i class="fa-solid fa-eye"></i> তালিকা</a>
@endsection

@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>বিলিং ও পেমেন্ট · INV-{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            <div class="detailgrid">
                <div><label>রোগী</label><strong>{{ $record->patient?->name ?? '#' . $record->patient_id }}</strong></div>
                <div><label>ছাড়</label><strong>{{ $record->discount }}</strong></div>
                <div><label>বিলের নোট</label><strong>{{ $record->notes }}</strong></div>
            </div>
            <div class="actions noprint">
                @if ($record->status === 'open' && \App\Support\Registry::can('invoices', true))
                    <a class="btn primary" href="{{ route('invoices.edit', $record) }}">সম্পাদনা</a>
                @endif
            </div>
        </div>
    </section>
    @include('billing.detail')
@endsection
