@extends('layouts.app', ['title' => 'বেড ও কেবিন'])
@section('heading-action')
    @if (\App\Support\Registry::can('beds', true))
        <a class="btn primary" href="{{ route('beds.create') }}">+ বেড যোগ</a>
    @endif
@endsection
@section('content')
    @php
        $stateLabels = [
            'occupied' => 'দখল',
            'available' => 'খালি',
            'cleaning' => 'পরিষ্কার চলছে',
            'inactive' => 'বন্ধ',
        ];
    @endphp
    <div class="beds">
        @foreach ($records as $bed)
            @php
                $stay = $bed->stays()->whereNull('ended_at')->with('admission.patient')->first();
                $stateKey = $stay ? 'occupied' : $bed->state;
            @endphp
            <article class="bed {{ $stay ? 'occupied' : ($bed->state === 'cleaning' ? 'clean' : '') }}">
                <div class="bedicon">▤</div>
                <span class="badge b-{{ $stateKey }}">{{ $stateLabels[$stateKey] ?? $stateKey }}</span>
                <h2>{{ $bed->name }}</h2>
                <p>{{ $bed->ward }}</p>
                <p>{{ $stay?->admission?->patient?->name }}</p>
                <p>{{ \App\Support\Format::money($bed->daily_rate) }} / ২৪ ঘণ্টা</p>
                <div class="actions">
                    <a class="btn" href="{{ route('beds.show', $bed) }}">বিস্তারিত</a>
                    @if ($stay && \App\Support\Registry::can('admissions'))
                        <a class="btn" href="{{ route('entities.show', ['admissions', $stay->admission_id]) }}">ভর্তি</a>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
    <div class="pagination">
        <span>মোট {{ $records->total() }} রেকর্ড</span>
        @if ($records->previousPageUrl())
            <a class="btn" href="{{ $records->previousPageUrl() }}">← আগের</a>
        @endif
        @if ($records->nextPageUrl())
            <a class="btn" href="{{ $records->nextPageUrl() }}">পরের →</a>
        @endif
    </div>
    <p class="hint">বেডভাড়া: শুরু হওয়া প্রতি ২৪ ঘণ্টা এক দিন; প্রতিটি bed stay আলাদা।</p>
@endsection
