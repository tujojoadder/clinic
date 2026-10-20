@extends('layouts.app', ['title' => 'টেস্ট ও ল্যাব'])
@section('heading-action')
    @if (\App\Support\Registry::can('lab_orders', true))
        <a class="btn primary" href="{{ route('lab_orders.create') }}">+ নতুন এন্ট্রি</a>
    @endif
@endsection
@section('content')
    <x-list-print title="টেস্ট ও ল্যাব তালিকা" :paginator="$records">
        <section class="panel list">
            @php
                $hasFilter = request()->filled('q') || request()->filled('test_id') || request()->filled('status');
            @endphp

            <div class="filters">
                {{-- Row 1: সার্চ + প্রতি পেজে কতটি + প্রিন্ট --}}
                <form method="get" id="labFilter">
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
                    <select class="js-select2" name="test_id" form="labFilter" data-label="টেস্ট"
                        onchange="this.form.submit()">
                        <option value="">সব টেস্ট</option>
                        @foreach ($tests as $t)
                            <option value="{{ $t->id }}" @selected(request('test_id') == $t->id)>{{ $t->name }}</option>
                        @endforeach
                    </select>

                    <select class="js-select2" name="status" form="labFilter" data-label="স্ট্যাটাস"
                        onchange="this.form.submit()">
                        <option value="">সব স্ট্যাটাস</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
                        @endforeach
                    </select>

                    <div class="filter-reset">
                        @if ($hasFilter)
                            <a class="btn"
                                href="{{ route('lab_orders.index', request()->filled('per_page') ? ['per_page' => per_page()] : []) }}">
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
                            <th>ID</th>
                            <th>রোগী</th>
                            <th>ভর্তি নম্বর</th>
                            <th>টেস্ট</th>
                            <th>অর্ডারের সময়</th>
                            <th>স্ট্যাটাস</th>
                            <th class="noprint">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>#{{ $record->id }}</td>
                                <td>{{ $record->patient?->name ?? '#' . $record->patient_id }}</td>
                                <td>{{ $record->admission_id ? 'ADM-' . $record->admission_id : '' }}</td>
                                <td>{{ $record->test?->name ?? '#' . $record->test_id }}</td>
                                <td>{{ datetime_to_view($record->ordered_at) }}</td>
                                <td><span class="badge s-{{ $record->status }}">{{ $record->status }}</span></td>
                                <td class="noprint">
                                    <a class="btn abtn view" href="{{ route('lab_orders.show', $record) }}"><i
                                            class="fa-solid fa-eye"></i> বিস্তারিত</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">কোনো তথ্য নেই। নতুন এন্ট্রি করুন।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination :paginator="$records" />
        </section>
    </x-list-print>
@endsection
