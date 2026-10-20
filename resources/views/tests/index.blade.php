@extends('layouts.app', ['title' => 'টেস্ট ও মূল্য'])
@section('heading-action')
    @if (\App\Support\Registry::can('tests', true))
        <a class="btn primary" href="{{ route('tests.create') }}">+ নতুন এন্ট্রি</a>
    @endif
@endsection
@section('content')
    <x-list-print title="টেস্টের তালিকা" :paginator="$records">
        <section class="panel list">
            @php
                $hasFilter = request()->filled('q') || request()->filled('sample_type') || request()->filled('active');
            @endphp

            <div class="filters">
                {{-- Row 1: সার্চ + প্রতি পেজে কতটি + প্রিন্ট --}}
                <form method="get" id="testFilter">
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

                {{-- Row 2: ড্রপডাউন ফিল্টার + রিসেট (ফর্মটা উপরের testFilter ফর্মেরই অংশ) --}}
                <div class="filter-row noprint">
                    <select class="js-select2" name="sample_type" form="testFilter" data-label="নমুনা"
                        onchange="this.form.submit()">
                        <option value="">সব নমুনা</option>
                        @foreach ($sampleTypes as $st)
                            <option value="{{ $st }}" @selected(request('sample_type') === $st)>{{ $st }}</option>
                        @endforeach
                    </select>

                    <select class="js-select2" name="active" form="testFilter" data-label="সক্রিয়"
                        onchange="this.form.submit()">
                        <option value="">সব (সক্রিয় ও নিষ্ক্রিয়)</option>
                        <option value="1" @selected(request('active') === '1')>সক্রিয়</option>
                        <option value="0" @selected(request('active') === '0')>নিষ্ক্রিয়</option>
                    </select>

                    {{-- রিসেট না থাকলেও জায়গা ধরে রাখে --}}
                    <div class="filter-reset">
                        @if ($hasFilter)
                            <a class="btn"
                                href="{{ route('tests.index', request()->filled('per_page') ? ['per_page' => per_page()] : []) }}">
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
                            <th>টেস্টের নাম</th>
                            <th>নমুনা</th>
                            <th>মূল্য</th>
                            <th>সময় (ঘণ্টা)</th>
                            <th>সক্রিয়</th>
                            <th class="noprint">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>#{{ $record->id }}</td>
                                <td>{{ $record->name }}</td>
                                <td>{{ $record->sample_type }}</td>
                                <td>{{ $record->price }}</td>
                                <td>{{ $record->turnaround_hours }}</td>
                                <td>{{ $record->active ? 'হ্যাঁ' : 'না' }}</td>
                                <td class="noprint">
                                    <a class="btn abtn view" href="{{ route('tests.show', $record) }}"><i
                                            class="fa-solid fa-eye"></i> বিস্তারিত</a>
                                    @if (\App\Support\Registry::can('tests', true))
                                        <a class="btn abtn edit" href="{{ route('tests.edit', $record) }}"><i
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
