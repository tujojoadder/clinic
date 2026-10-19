@props([
    'title',
    'from' => null,
    'to' => null,
    // পরে নিজের ইমেজের লিংক দিন
    'header' => asset('assets/images/header.png'),
    'footer' => asset('assets/images/footer.png'),
    'watermark' => asset('assets/logo.png'),
])

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/report_print.css') }}?v=2">
    @endpush
@endonce

{{-- প্রিন্টে প্রতি পেজে একই জায়গায় বসে (স্ক্রিনে লুকানো) --}}
<div class="rp-fixed rp-top"><img src="{{ $header }}" alt=""></div>
<div class="rp-fixed rp-bottom"><img src="{{ $footer }}" alt=""></div>
<div class="rp-watermark"><img src="{{ $watermark }}" alt=""></div>

{{-- আসল table: thead/tfoot প্রতি পেজে হেডার-ফুটারের জায়গা ফাঁকা রাখে, tbody স্বাভাবিকভাবে পেজ ভাঙে --}}
<table class="rp-frame">
    <thead class="rp-thead">
        <tr>
            <td>
                <div class="rp-space-top"></div>
            </td>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td class="rp-content">
                <div class="rp-docinfo">
                    <h1 class="rp-title">{{ $title }}</h1>
                    <div class="rp-meta">
                        <div>
                            <span>রিপোর্টের সময়কাল</span>
                            <b>
                                @if ($from && $to)
                                    {{ date_to_view($from) }} থেকে {{ date_to_view($to) }} পর্যন্ত
                                @else
                                    আজ পর্যন্ত ({{ date_to_view(today()) }})
                                @endif
                            </b>
                        </div>
                        <div>
                            <span>প্রিন্টের সময়</span>
                            <b>{{ datetime_to_view(now()) }}</b>
                        </div>
                        <div>
                            <span>প্রিন্ট করেছেন</span>
                            <b>{{ auth()->user()->name ?? '' }}</b>
                        </div>
                    </div>
                </div>

                {{ $slot }}
            </td>
        </tr>
    </tbody>

    <tfoot class="rp-tfoot">
        <tr>
            <td>
                <div class="rp-space-bottom"></div>
            </td>
        </tr>
    </tfoot>
</table>
