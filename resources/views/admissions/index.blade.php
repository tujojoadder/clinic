@extends('layouts.app', ['title' => 'রোগী ভর্তি'])
@section('heading-action')
    @if (\App\Support\Registry::can('admissions', true))
        <a class="btn primary" href="{{ route('admissions.create') }}">+ নতুন এন্ট্রি</a>
    @endif
@endsection
@section('content')
    <x-list-print title="ভর্তির তালিকা" :paginator="$records">
        <section class="panel list">
            @php
                $hasFilter =
                    request()->filled('q') ||
                    request()->filled('specialty_id') ||
                    request()->filled('doctor_id') ||
                    request()->filled('status');
            @endphp

            <div class="filters">
                {{-- Row 1: সার্চ + প্রতি পেজে কতটি + প্রিন্ট --}}
                <form method="get" id="admissionFilter">
                    {{-- খোঁজার সময় প্রতি পেজে কতটি সেটা যেন হারিয়ে না যায় --}}
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

                {{-- Row 2: ড্রপডাউন ফিল্টার + রিসেট (ফর্মটা উপরের admissionFilter ফর্মেরই অংশ) --}}
                <div class="filter-row noprint">
                    <select class="js-select2" name="specialty_id" form="admissionFilter" data-label="স্পেশালিটি"
                        onchange="this.form.submit()">
                        <option value="">সব স্পেশালিটি</option>
                        @foreach ($specialties as $s)
                            <option value="{{ $s->id }}" @selected((string) request('specialty_id') === (string) $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>

                    <select class="js-select2" name="doctor_id" form="admissionFilter" data-label="ডাক্তার"
                        onchange="this.form.submit()">
                        <option value="">সব ডাক্তার</option>
                        @foreach ($doctors as $d)
                            <option value="{{ $d->id }}" @selected((string) request('doctor_id') === (string) $d->id)>{{ $d->name }}</option>
                        @endforeach
                    </select>

                    <select class="js-select2" name="status" form="admissionFilter" data-label="স্ট্যাটাস"
                        onchange="this.form.submit()">
                        <option value="">সব স্ট্যাটাস</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
                        @endforeach
                    </select>

                    {{-- রিসেট না থাকলেও জায়গা ধরে রাখে --}}
                    <div class="filter-reset">
                        @if ($hasFilter)
                            <a class="btn"
                                href="{{ route('admissions.index', request()->filled('per_page') ? ['per_page' => per_page()] : []) }}">
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
                            <th>ডাক্তার</th>
                            <th>স্পেশালিটি</th>
                            <th>ভর্তির তারিখ ও সময়</th>
                            <th>ভর্তির কারণ</th>
                            <th>স্ট্যাটাস</th>
                            <th class="noprint">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>#{{ $record->id }}</td>
                                <td>{{ $record->patient?->name ?? '#' . $record->patient_id }}</td>
                                <td>{{ $record->doctor?->name ?? '#' . $record->doctor_id }}</td>
                                <td>{{ $record->doctor?->specialty?->name ?? '—' }}</td>
                                <td>{{ $record->admitted_at ? \Illuminate\Support\Carbon::parse($record->admitted_at)->format('d-m-Y h:i A') : '—' }}
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($record->reason, 45) }}</td>
                                <td><span class="badge s-{{ $record->status }}">{{ $record->status }}</span></td>
                                <td class="noprint">
                                    <a class="btn abtn view" href="{{ route('admissions.show', $record) }}"><i
                                            class="fa-solid fa-eye"></i> বিস্তারিত</a>
                                    @if ($record->status === 'admitted' && \App\Support\Registry::can('admissions', true))
                                        <a class="btn abtn edit" href="{{ route('admissions.edit', $record) }}"><i
                                                class="fa-solid fa-pen-to-square"></i> এডিট</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">কোনো তথ্য নেই। নতুন এন্ট্রি করুন।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination :paginator="$records" />
        </section>
    </x-list-print>
@endsection
