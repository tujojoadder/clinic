@extends('layouts.app', ['title' => 'রেফারেন্স · #' . $record->id])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>রেফারেন্স · #{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            <div class="detailgrid">
                <div><label>নাম</label><strong>{{ $record->name }}</strong></div>
                <div><label>মোবাইল</label><strong>{{ $record->phone }}</strong></div>
                <div><label>কমিশন (%)</label><strong>{{ $record->commission_percent }}</strong></div>
                <div><label>জয়েনিং ডেট</label><strong>{{ $record->joining_date }}</strong></div>
            </div>
            <div class="actions noprint">
                <a class="btn" href="{{ route('references.index') }}">তালিকা</a>
                @if (\App\Support\Registry::can('references', true))
                    <a class="btn" href="{{ route('references.edit', $record) }}">সম্পাদনা</a>
                    <form method="post" action="{{ route('references.destroy', $record) }}" data-confirm="রেকর্ড মুছবেন?">
                        @csrf @method('DELETE')
                        <button class="btn danger">ডিলেট</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
