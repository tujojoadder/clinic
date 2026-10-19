@extends('layouts.app', ['title' => 'দৈনিক আদায়', 'module' => 'reports_daily'])
@section('content')
    <form method="get" class="filters noprint">
        <input type="text" name="from" class="js-date" value="{{ date_to_view($from) }}">
        <input type="text" name="to" class="js-date" value="{{ date_to_view($to) }}">
        <button class="btn primary">রিপোর্ট দেখুন</button>
        <button type="button" class="btn" data-print>প্রিন্ট</button>
    </form>

    <section class="panel">
        <div class="panelhead has-tools">
            <h2>দৈনিক আদায়</h2>
            <x-per-page :paginator="$daily" />
        </div>
        <table>
            <tr>
                <th>তারিখ</th>
                <th>নিট আদায়</th>
            </tr>
            @forelse ($daily as $row)
                <tr>
                    <td>{{ date_to_view($row->day) }}</td>
                    <td>{{ \App\Support\Format::money($row->amount) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">এই সময়ে কোনো আদায় নেই।</td>
                </tr>
            @endforelse
        </table>
        <x-pagination :paginator="$daily" />
    </section>
@endsection
