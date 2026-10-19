@extends('layouts.app', ['title' => 'টেস্ট বিক্রি', 'module' => 'reports_sales'])
@section('content')
    <form method="get" class="filters noprint">
        <input type="text" name="from" class="js-date" value="{{ date_to_view($from) }}">
        <input type="text" name="to" class="js-date" value="{{ date_to_view($to) }}">
        <button class="btn primary">রিপোর্ট দেখুন</button>
        <button type="button" class="btn" data-print>প্রিন্ট</button>
    </form>

    <x-report-print title="টেস্ট বিক্রি রিপোর্ট" :from="$from" :to="$to">
        <section class="panel">
            <div class="panelhead has-tools">
                <h2>টেস্ট বিক্রি</h2>
                <x-per-page :paginator="$sales" />
            </div>
            <table>
                <thead>
                    <tr>
                        <th>টেস্ট</th>
                        <th class="num">সংখ্যা</th>
                        <th class="num">চার্জ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td class="num">{{ $row->count }}</td>
                            <td class="num">{{ \App\Support\Format::money($row->amount) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">এই সময়ে কোনো টেস্ট বিক্রি নেই।</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <x-pagination :paginator="$sales" />
        </section>
    </x-report-print>
@endsection
