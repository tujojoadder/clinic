@extends('layouts.app', ['title' => 'ডাক্তারের প্রাপ্য', 'module' => 'reports_doctors'])
@section('content')
    <div class="filters noprint">
        <button type="button" class="btn" data-print>প্রিন্ট</button>
    </div>

    <x-report-print title="ডাক্তারের প্রাপ্য রিপোর্ট">
        <section class="panel">
            <div class="panelhead has-tools">
                <h2>ডাক্তারের প্রাপ্য</h2>
                <x-per-page :paginator="$doctorBalances" />
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ডাক্তার</th>
                        <th class="num">বাকি প্রাপ্য</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($doctorBalances as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td class="num">{{ \App\Support\Format::money($row->balance) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">কোনো ডাক্তার নেই।</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <x-pagination :paginator="$doctorBalances" />
        </section>
    </x-report-print>
@endsection
