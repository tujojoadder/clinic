@extends('layouts.app', ['title' => 'টেস্ট রিপোর্ট টেমপ্লেট · #' . $record->id])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>টেস্ট রিপোর্ট টেমপ্লেট · #{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            <div class="detailgrid">
                <div><label>টেস্ট</label><strong>{{ $record->test?->name ?? '#' . $record->test_id }}</strong></div>
                <div><label>প্যারামিটার</label><strong>{{ $record->name }}</strong></div>
                <div><label>ইউনিট</label><strong>{{ $record->unit }}</strong></div>
                <div><label>যাচাইকৃত রেফারেন্স রেঞ্জ</label><strong>{{ $record->reference_range }}</strong></div>
                <div><label>ক্রম</label><strong>{{ $record->sort_order }}</strong></div>
            </div>
            <div class="actions noprint">
                <a class="btn" href="{{ route('test_parameters.index') }}">তালিকা</a>
                @if (\App\Support\Registry::can('test_parameters', true))
                    <a class="btn" href="{{ route('test_parameters.edit', $record) }}">সম্পাদনা</a>
                    <form method="post" action="{{ route('test_parameters.destroy', $record) }}"
                        data-confirm="রেকর্ড মুছবেন?">
                        @csrf @method('DELETE')
                        <button class="btn danger">ডিলেট</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
