@extends('layouts.app', ['title' => 'ডাক্তারের প্রাপ্য', 'module' => 'reports_doctors'])
@section('content')
    <div class="filters noprint">
        <button type="button" class="btn" data-print>প্রিন্ট</button>
    </div>

    <section class="panel">
        <div class="panelhead has-tools">
            <h2>ডাক্তারের প্রাপ্য</h2>
            <x-per-page :paginator="$doctorBalances" />
        </div>
        <table>
            <tr>
                <th>ডাক্তার</th>
                <th>বাকি প্রাপ্য</th>
            </tr>
            @forelse ($doctorBalances as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>{{ \App\Support\Format::money($row->balance) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">কোনো ডাক্তার নেই।</td>
                </tr>
            @endforelse
        </table>
        <x-pagination :paginator="$doctorBalances" />
    </section>
@endsection
