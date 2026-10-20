@extends('layouts.app', ['title' => 'বিলিং ও পেমেন্ট'])
@section('heading-action')
    @if (\App\Support\Registry::can('invoices', true))
        <a class="btn primary" href="{{ route('invoices.create') }}">+ নতুন এন্ট্রি</a>
    @endif
@endsection
@section('content')
    <x-list-print title="বিলের তালিকা" :paginator="$records">
        <section class="panel list">
            @php
                $hasFilter = request()->filled('q') || request()->filled('patient_id') || request()->filled('status');
            @endphp

            <div class="filters">
                {{-- Row 1: সার্চ + প্রতি পেজে কতটি + প্রিন্ট --}}
                <form method="get" id="invoiceFilter">
                    @if (request()->filled('per_page'))
                        <input type="hidden" name="per_page" value="{{ per_page() }}">
                    @endif
                    <div class="sbox {{ request()->filled('q') ? 'has' : '' }}">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input name="q" value="{{ request('q') }}" placeholder="তথ্য দিয়ে খুঁজুন"
                            oninput="this.parentNode.classList.toggle('has', this.value !== '')">
                        <button type="button" class="sclear" title="মুছুন"
                            onclick="var f=this.form;f.q.value='';@if (request()->filled('q')) f.submit();@else this.parentNode.classList.remove('has');f.q.focus(); @endif"><i
                                class="fa-solid fa-xmark"></i></button>
                    </div>
                    <button class="btn"><i class="fa-solid fa-magnifying-glass"></i> খুঁজুন</button>
                </form>
                <div class="filters-right">
                    <x-per-page :paginator="$records" />
                    <x-print-button />
                </div>

                {{-- Row 2: ড্রপডাউন ফিল্টার + রিসেট --}}
                <div class="filter-row noprint">
                    {{-- রোগী: ajax select2 (visits ফর্মের মতো) --}}
                    <select id="filter_patient_id" name="patient_id" form="invoiceFilter"
                        data-ajax-url="{{ route('ajax.patients.search') }}" data-placeholder="সব রোগী" data-label="রোগী"
                        onchange="this.form.submit()">
                        <option></option>
                        @if ($filterPatient)
                            <option value="{{ $filterPatient->id }}" selected>{{ $filterPatient->name }}</option>
                        @endif
                    </select>

                    <select class="js-select2" name="status" form="invoiceFilter" data-label="স্ট্যাটাস"
                        onchange="this.form.submit()">
                        <option value="">সব স্ট্যাটাস</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
                        @endforeach
                    </select>

                    <div class="filter-reset">
                        @if ($hasFilter)
                            <a class="btn"
                                href="{{ route('invoices.index', request()->filled('per_page') ? ['per_page' => per_page()] : []) }}">
                                <i class="fa-solid fa-rotate-left"></i> রিসেট
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="tablewrap">
                <table>
                    <thead>
                        <tr>
                            <th>বিল</th>
                            <th>রোগী</th>
                            <th>ছাড়</th>
                            <th>বিলের নোট</th>
                            <th>স্ট্যাটাস</th>
                            <th class="noprint">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>INV-{{ $record->id }}</td>
                                <td>{{ $record->patient?->name ?? '#' . $record->patient_id }}</td>
                                <td>{{ $record->discount }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($record->notes, 45) }}</td>
                                <td><span class="badge s-{{ $record->status }}">{{ $record->status }}</span></td>
                                <td class="noprint">
                                    <a class="btn abtn view" href="{{ route('invoices.show', $record) }}"><i
                                            class="fa-solid fa-eye"></i> বিস্তারিত</a>
                                    @if ($record->status === 'open' && \App\Support\Registry::can('invoices', true))
                                        <a class="btn abtn edit" href="{{ route('invoices.edit', $record) }}"><i
                                                class="fa-solid fa-pen-to-square"></i> এডিট</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">কোনো তথ্য নেই। নতুন এন্ট্রি করুন।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination :paginator="$records" />
        </section>
    </x-list-print>
@endsection
