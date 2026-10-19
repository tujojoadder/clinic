@extends('layouts.app', ['title' => 'বর্তমান বকেয়া', 'module' => 'reports_dues'])
@section('content')
    <div class="filters noprint">
        <button type="button" class="btn" data-print>প্রিন্ট</button>
    </div>

    <x-report-print title="বর্তমান বকেয়া রিপোর্ট">
        <section class="panel">
            <div class="panelhead has-tools">
                <h2>বর্তমান বকেয়া</h2>
                <x-per-page :paginator="$dues" />
            </div>
            <table>
                <thead>
                    <tr>
                        <th>রোগী</th>
                        <th>বিল</th>
                        <th class="num">বকেয়া</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dues as $row)
                        <tr>
                            <td>{{ $row['invoice']->patient->name }}</td>
                            <td>
                                <a
                                    href="{{ route('entities.show', ['invoices', $row['invoice']->id]) }}">INV-{{ $row['invoice']->id }}</a>
                            </td>
                            <td class="num">{{ \App\Support\Format::money($row['due']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">কোনো বকেয়া নেই।</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <x-pagination :paginator="$dues" />
        </section>
    </x-report-print>
@endsection
