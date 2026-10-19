@extends('layouts.app', ['title' => 'দৈনিক আদায়', 'module' => 'reports_daily'])
@section('content')
    <form method="get" class="filters noprint">
        <input type="text" name="from" class="js-date" value="{{ date_to_view($from) }}">
        <input type="text" name="to" class="js-date" value="{{ date_to_view($to) }}">
        <button class="btn primary">রিপোর্ট দেখুন</button>
        <button type="button" class="btn" data-print>প্রিন্ট</button>
    </form>

    <x-report-print title="দৈনিক আদায় রিপোর্ট" :from="$from" :to="$to">
        <section class="panel">
            <div class="panelhead has-tools">
                <h2>দৈনিক আদায়</h2>
                <x-per-page :paginator="$daily" />
            </div>
            <table>
                <thead>
                    <tr>
                        <th>তারিখ</th>
                        <th class="num">নিট আদায়</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daily as $row)
                        <tr>
                            <td>{{ date_to_view($row->day) }}</td>
                            <td class="num">{{ \App\Support\Format::money($row->amount) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">এই সময়ে কোনো আদায় নেই।</td>
                        </tr>
                    @endforelse

                    @if ($daily->count() > 0)
                        <tr class="total-row">
                            <td>মোট</td>
                            <td class="num">{{ \App\Support\Format::money($daily->sum('amount')) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <x-pagination :paginator="$daily" />
        </section>
    </x-report-print>
@endsection
