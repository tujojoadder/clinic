@extends('layouts.app', ['title' => 'বেড ও কেবিন · #' . $record->id])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>বেড ও কেবিন · #{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            @php
                $types = ['ward' => 'সাধারণ', 'cabin' => 'কেবিন', 'icu' => 'ICU'];
                $states = ['available' => 'খালি', 'cleaning' => 'পরিষ্কার', 'inactive' => 'নিষ্ক্রিয়'];
            @endphp
            <div class="detailgrid">
                <div><label>বেড / কেবিন নম্বর</label><strong>{{ $record->name }}</strong></div>
                <div><label>ওয়ার্ড</label><strong>{{ $record->ward }}</strong></div>
                <div><label>ধরন</label><strong>{{ $types[$record->type] ?? $record->type }}</strong></div>
                <div><label>প্রতি ২৪ ঘণ্টার ভাড়া</label><strong>{{ $record->daily_rate }}</strong></div>
                <div><label>অবস্থা</label><strong>{{ $states[$record->state] ?? $record->state }}</strong></div>
            </div>
            <div class="actions noprint">
                <a class="btn" href="{{ route('beds.index') }}">তালিকা</a>
                @if (\App\Support\Registry::can('beds', true))
                    <a class="btn" href="{{ route('beds.edit', $record) }}">সম্পাদনা</a>
                    <form method="post" action="{{ route('beds.destroy', $record) }}" data-confirm="রেকর্ড মুছবেন?">
                        @csrf @method('DELETE')
                        <button class="btn danger">ডিলেট</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
