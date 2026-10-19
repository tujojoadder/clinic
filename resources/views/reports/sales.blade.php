@extends('layouts.app', ['title' => 'টেস্ট বিক্রি', 'module' => 'reports_sales'])
@section('content')
    <form method="get" class="filters noprint">
        <input type="text" name="from" class="js-date" value="{{ date_to_view($from) }}">
        <input type="text" name="to" class="js-date" value="{{ date_to_view($to) }}">
        <button class="btn primary">রিপোর্ট দেখুন</button>
        <button type="button" class="btn" data-print>প্রিন্ট</button>
    </form>

    <section class="panel">
        <div class="panelhead">
            <h2>টেস্ট বিক্রি</h2>
        </div>
        <table>
            <tr>
                <th>টেস্ট</th>
                <th>সংখ্যা</th>
                <th>চার্জ</th>
            </tr>
            @foreach ($sales as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->count }}</td>
                    <td>{{ \App\Support\Format::money($row->amount) }}</td>
                </tr>
            @endforeach
        </table>
    </section>
@endsection
