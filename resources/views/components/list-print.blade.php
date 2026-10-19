@props([
    'title',
    'paginator' => null, // দিলে "মোট কত রেকর্ড / এই পেজে কত থেকে কত" দেখায়
    'filters' => [], // ঐচ্ছিক: ['লেবেল' => 'মান'] — ফিল্টার ফর্ম থেকে নিজে থেকেই আসে, তাই সাধারণত লাগে না
    'header' => asset('assets/images/header.png'),
    'footer' => asset('assets/images/footer.png'),
    'watermark' => asset('assets/images/watermark.png'),
])

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/list_print.css') }}?v=1">
    @endpush
    <script src="{{ asset('assets/js/list_print.js') }}?v=1" defer></script>
@endonce

{{-- প্রিন্টে প্রতি পেজে একই জায়গায় বসে (স্ক্রিনে লুকানো) --}}
<div class="lp-fixed lp-top"><img src="{{ $header }}" alt=""></div>
<div class="lp-fixed lp-bottom"><img src="{{ $footer }}" alt=""></div>
<div class="lp-watermark"><img src="{{ $watermark }}" alt=""></div>

{{-- আসল table: thead/tfoot প্রতি পেজে হেডার-ফুটারের জায়গা ফাঁকা রাখে, tbody স্বাভাবিকভাবে পেজ ভাঙে --}}
<table class="lp-frame">
    <thead class="lp-thead">
        <tr>
            <td>
                <div class="lp-space-top"></div>
            </td>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td class="lp-content">
                <div class="lp-docinfo">
                    <h1 class="lp-title">{{ $title }}</h1>
                    <div class="lp-meta">
                        @if ($paginator)
                            <div>
                                <span>রেকর্ড</span>
                                <b>
                                    মোট {{ $paginator->total() }} টি
                                    @if ($paginator->total() > 0)
                                        (এই পেজে {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }})
                                    @endif
                                </b>
                            </div>
                        @endif
                        <div>
                            <span>প্রিন্টের সময়</span>
                            <b>{{ datetime_to_view(now()) }}</b>
                        </div>
                        <div>
                            <span>প্রিন্ট করেছেন</span>
                            <b>{{ auth()->user()->name ?? '' }}</b>
                        </div>
                    </div>

                    {{-- ব্যবহৃত ফিল্টার: list_print.js প্রিন্টের আগে এখানে বসায় --}}
                    <div class="lp-filterbox" data-lp-filterbox>
                        <span class="lp-filterlabel">ফিল্টার</span>
                        <span class="lp-chips" data-lp-chips>
                            @foreach ($filters as $label => $value)
                                @if (filled($value))
                                    <span class="lp-chip"><b>{{ $label }}:</b> {{ $value }}</span>
                                @endif
                            @endforeach
                        </span>
                        <span class="lp-nofilter">কোনো ফিল্টার নেই (সব রেকর্ড)</span>
                    </div>
                </div>

                {{ $slot }}
            </td>
        </tr>
    </tbody>

    <tfoot class="lp-tfoot">
        <tr>
            <td>
                <div class="lp-space-bottom"></div>
            </td>
        </tr>
    </tfoot>
</table>
