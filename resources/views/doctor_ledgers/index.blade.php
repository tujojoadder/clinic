@extends('layouts.app', ['title' => 'ডাক্তারের হিসাব'])
@section('heading-action')
    @if (\App\Support\Registry::can('doctor_ledgers', true))
        <a class="btn primary" href="{{ route('doctor_ledgers.create') }}">+ নতুন এন্ট্রি</a>
    @endif
@endsection
@section('content')
    <x-list-print title="ডাক্তারের হিসাবের তালিকা" :paginator="$records">
        <section class="panel list">
            @php
                $types = ['payable' => 'প্রাপ্য', 'settlement' => 'পরিশোধ'];
                $hasFilter = request()->filled('q') || request()->filled('doctor_id') || request()->filled('type');
            @endphp

            <div class="filters">
                {{-- Row 1: সার্চ + প্রতি পেজে কতটি + প্রিন্ট --}}
                <form method="get" id="ledgerFilter">
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
                    <select class="js-select2" name="doctor_id" form="ledgerFilter" data-label="ডাক্তার"
                        onchange="this.form.submit()">
                        <option value="">সব ডাক্তার</option>
                        @foreach ($doctors as $d)
                            <option value="{{ $d->id }}" @selected(request('doctor_id') == $d->id)>{{ $d->name }}</option>
                        @endforeach
                    </select>

                    <select class="js-select2" name="type" form="ledgerFilter" data-label="এন্ট্রি"
                        onchange="this.form.submit()">
                        <option value="">সব এন্ট্রি</option>
                        @foreach ($types as $key => $label)
                            <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <div class="filter-reset">
                        @if ($hasFilter)
                            <a class="btn"
                                href="{{ route('doctor_ledgers.index', request()->filled('per_page') ? ['per_page' => per_page()] : []) }}">
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
                            <th>ডাক্তার</th>
                            <th>এন্ট্রি</th>
                            <th>পরিমাণ</th>
                            <th>তারিখ</th>
                            <th>বিবরণ</th>
                            <th class="noprint">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>#{{ $record->id }}</td>
                                <td>{{ $record->doctor?->name ?? '#' . $record->doctor_id }}</td>
                                <td>{{ $types[$record->type] ?? $record->type }}</td>
                                <td>{{ $record->amount }}</td>
                                <td>{{ date_to_view($record->entry_date) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($record->notes, 45) }}</td>
                                <td class="noprint">
                                    <a class="btn abtn view" href="{{ route('doctor_ledgers.show', $record) }}"><i
                                            class="fa-solid fa-eye"></i> বিস্তারিত</a>
                                    @if (\App\Support\Registry::can('doctor_ledgers', true))
                                        <a class="btn abtn edit" href="{{ route('doctor_ledgers.edit', $record) }}"><i
                                                class="fa-solid fa-pen-to-square"></i> এডিট</a>
                                    @endif
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
