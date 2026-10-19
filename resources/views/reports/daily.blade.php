@extends('layouts.app', ['title' => 'দৈনিক আদায়', 'module' => 'reports_daily'])
@section('content')
    <form method="get" class="filters noprint">
        <input type="text" name="from" class="js-date" value="{{ date_to_view($from) }}">
        <input type="text" name="to" class="js-date" value="{{ date_to_view($to) }}">
        <button class="btn primary">রিপোর্ট দেখুন</button>
        <button type="button" class="btn" data-print>প্রিন্ট</button>
    </form>

    <section class="panel">
        <div class="panelhead">
            <h2>দৈনিক আদায়</h2>
        </div>
        <table>
            <tr>
                <th>তারিখ</th>
                <th>নিট আদায়</th>
            </tr>
            @foreach ($daily as $row)
                <tr>
                    <td>{{ $row->day }}</td>
                    <td>{{ \App\Support\Format::money($row->amount) }}</td>
                </tr>
            @endforeach
        </table>
    </section>
@endsection
