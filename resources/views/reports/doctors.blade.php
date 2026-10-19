@extends('layouts.app', ['title' => 'ডাক্তারের প্রাপ্য', 'module' => 'reports_doctors'])
@section('content')
    <div class="filters noprint">
        <button type="button" class="btn" data-print>প্রিন্ট</button>
    </div>

    <section class="panel">
        <div class="panelhead">
            <h2>ডাক্তারের প্রাপ্য</h2>
        </div>
        <table>
            <tr>
                <th>ডাক্তার</th>
                <th>বাকি প্রাপ্য</th>
            </tr>
            @foreach ($doctorBalances as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>{{ \App\Support\Format::money($row->balance) }}</td>
                </tr>
            @endforeach
        </table>
    </section>
@endsection
