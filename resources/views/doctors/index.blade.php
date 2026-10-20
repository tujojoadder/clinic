@extends('layouts.app', ['title' => 'ডাক্তার'])
@section('heading-action')
    @if (\App\Support\Registry::can('doctors', true))
        <a class="btn primary" href="{{ route('doctors.create') }}">+ নতুন এন্ট্রি</a>
    @endif
@endsection
@section('content')
    <x-list-print title="ডাক্তারের তালিকা" :paginator="$records">
        <section class="panel list">
            @php
                $hasFilter = request()->filled('q') || request()->filled('specialty_id') || request()->filled('active');
            @endphp

            <div class="filters">
                {{-- Row 1: সার্চ + প্রতি পেজে কতটি + প্রিন্ট --}}
                <form method="get" id="doctorFilter">
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
                    <select class="js-select2" name="specialty_id" form="doctorFilter" data-label="স্পেশালিটি"
                        onchange="this.form.submit()">
                        <option value="">সব স্পেশালিটি</option>
                        @foreach ($specialties as $s)
                            <option value="{{ $s->id }}" @selected(request('specialty_id') == $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>

                    <select class="js-select2" name="active" form="doctorFilter" data-label="সক্রিয়"
                        onchange="this.form.submit()">
                        <option value="">সব (সক্রিয় ও নিষ্ক্রিয়)</option>
                        <option value="1" @selected(request('active') === '1')>সক্রিয়</option>
                        <option value="0" @selected(request('active') === '0')>নিষ্ক্রিয়</option>
                    </select>

                    <div class="filter-reset">
                        @if ($hasFilter)
                            <a class="btn"
                                href="{{ route('doctors.index', request()->filled('per_page') ? ['per_page' => per_page()] : []) }}">
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
                            <th>নাম</th>
                            <th>স্পেশালিটি</th>
                            <th>মোবাইল</th>
                            <th>ভিজিট ফি</th>
                            <th>ফলোআপ ফি</th>
                            <th>বসার দিন</th>
                            <th class="noprint">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>#{{ $record->id }}</td>
                                <td>{{ $record->name }}</td>
                                <td>{{ $record->specialty?->name }}</td>
                                <td>{{ $record->phone }}</td>
                                <td>{{ $record->fee }}</td>
                                <td>{{ $record->followup_fee }}</td>
                                <td>{{ $record->days->pluck('name')->implode(', ') }}</td>
                                <td class="noprint">
                                    <a class="btn abtn view" href="{{ route('doctors.show', $record) }}"><i
                                            class="fa-solid fa-eye"></i> বিস্তারিত</a>
                                    @if (\App\Support\Registry::can('doctors', true))
                                        <a class="btn abtn edit" href="{{ route('doctors.edit', $record) }}"><i
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
