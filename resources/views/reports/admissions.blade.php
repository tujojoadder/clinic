@extends('layouts.app', ['title' => 'ভর্তি ও ডিসচার্জ', 'module' => 'reports_admissions'])
@section('content')
    <form method="get" class="filters noprint">
        <input type="text" name="from" class="js-date" value="{{ date_to_view($from) }}">
        <input type="text" name="to" class="js-date" value="{{ date_to_view($to) }}">
        <button class="btn primary">রিপোর্ট দেখুন</button>
        <button type="button" class="btn" data-print>প্রিন্ট</button>
    </form>
    <section class="panel">
        <div class="panelhead">
            <h2>ভর্তি ও ডিসচার্জ</h2>
        </div>
        <div class="panelbody">ভর্তি: {{ $admitted }} · ডিসচার্জ: {{ $discharged }}</div>
    </section>

    @php
        $statusLabels = ['admitted' => 'ভর্তি আছে', 'discharged' => 'ডিসচার্জ', 'cancelled' => 'বাতিল'];
        $typeLabels = ['regular' => 'নিয়মিত', 'transfer' => 'ট্রান্সফার', 'own_risk' => 'নিজ দায়িত্বে'];
    @endphp

    <section class="panel">
        <div class="panelhead">
            <h2>ভর্তি হওয়া রোগী</h2>
        </div>
        <table>
            <tr>
                <th>ভর্তি নং</th>
                <th>রোগী</th>
                <th>ডাক্তার</th>
                <th>ভর্তির সময়</th>
                <th>অবস্থা</th>
            </tr>
            @forelse ($admittedList as $row)
                <tr>
                    <td><a href="{{ route('admissions.show', $row->id) }}">ADM-{{ $row->id }}</a></td>
                    <td>{{ $row->patient }}</td>
                    <td>{{ $row->doctor }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->admitted_at)->format('d-m-Y h:i A') }}</td>
                    <td>{{ $statusLabels[$row->status] ?? $row->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">এই সময়ে কেউ ভর্তি হয়নি।</td>
                </tr>
            @endforelse
        </table>
    </section>

    <section class="panel">
        <div class="panelhead">
            <h2>ডিসচার্জ হওয়া রোগী</h2>
        </div>
        <table>
            <tr>
                <th>ভর্তি নং</th>
                <th>রোগী</th>
                <th>ডাক্তার</th>
                <th>ডিসচার্জের সময়</th>
                <th>ধরন</th>
            </tr>
            @forelse ($dischargedList as $row)
                <tr>
                    <td><a href="{{ route('admissions.show', $row->id) }}">ADM-{{ $row->id }}</a></td>
                    <td>{{ $row->patient }}</td>
                    <td>{{ $row->doctor }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->discharged_at)->format('d-m-Y h:i A') }}</td>
                    <td>{{ $typeLabels[$row->discharge_type] ?? $row->discharge_type }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">এই সময়ে কেউ ডিসচার্জ হয়নি।</td>
                </tr>
            @endforelse
        </table>
    </section>
@endsection
