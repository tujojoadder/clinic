@extends('layouts.app', ['title' => 'বর্তমান বকেয়া', 'module' => 'reports_dues'])
@section('content')
    <div class="filters noprint">
        <button type="button" class="btn" data-print>প্রিন্ট</button>
    </div>

    <section class="panel">
        <div class="panelhead has-tools">
            <h2>বর্তমান বকেয়া</h2>
            <x-per-page :paginator="$dues" />
        </div>
        <table>
            <tr>
                <th>রোগী</th>
                <th>বিল</th>
                <th>বকেয়া</th>
            </tr>
            @forelse ($dues as $row)
                <tr>
                    <td>{{ $row['invoice']->patient->name }}</td>
                    <td>
                        <a
                            href="{{ route('entities.show', ['invoices', $row['invoice']->id]) }}">INV-{{ $row['invoice']->id }}</a>
                    </td>
                    <td>{{ \App\Support\Format::money($row['due']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">কোনো বকেয়া নেই।</td>
                </tr>
            @endforelse
        </table>
        <x-pagination :paginator="$dues" />
    </section>
@endsection
