@extends('layouts.app', ['title' => 'আদায় ও ব্যয়', 'module' => 'reports_income'])
@section('content')
    <form method="get" class="filters noprint">
        <input type="text" name="from" class="js-date" value="{{ date_to_view($from) }}">
        <input type="text" name="to" class="js-date" value="{{ date_to_view($to) }}">
        <button class="btn primary">রিপোর্ট দেখুন</button>
        <button type="button" class="btn" data-print>প্রিন্ট</button>
    </form>

    <x-report-print title="আদায় ও ব্যয় রিপোর্ট" :from="$from" :to="$to">
        <section class="panel">
            <div class="panelhead">
                <h2>আদায় ও ব্যয়</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>বিবরণ</th>
                        <th class="num">পরিমাণ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>নিট আদায়</td>
                        <td class="num">{{ \App\Support\Format::money($income) }}</td>
                    </tr>
                    <tr>
                        <td>ব্যয়</td>
                        <td class="num">{{ \App\Support\Format::money($expense) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>আদায় বাদ ব্যয় (লাভ নয়)</td>
                        <td class="num">{{ \App\Support\Format::money($income - $expense) }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </x-report-print>
@endsection
