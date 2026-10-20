@extends('layouts.app', ['title' => 'নার্সিং রেকর্ড'])
@section('heading-action')
    @if (\App\Support\Registry::can('nursing_records', true))
        <a class="btn primary" href="{{ route('nursing_records.create') }}">+ নতুন এন্ট্রি</a>
    @endif
@endsection
@section('content')
    <x-list-print title="নার্সিং রেকর্ডের তালিকা" :paginator="$records">
        <section class="panel list">
            <div class="filters">
                {{-- সার্চ + প্রতি পেজে কতটি + প্রিন্ট --}}
                <form method="get" id="nursingFilter">
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
            </div>

            <div class="tablewrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ভর্তি</th>
                            <th>পর্যবেক্ষণের সময়</th>
                            <th>রক্তচাপ (mmHg)</th>
                            <th>তাপমাত্রা (°F)</th>
                            <th>পালস</th>
                            <th class="noprint">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>#{{ $record->id }}</td>
                                <td>ADM-{{ $record->admission_id }} · {{ $record->admission?->patient?->name }}</td>
                                <td>{{ datetime_to_view($record->recorded_at) }}</td>
                                <td>{{ $record->blood_pressure }}</td>
                                <td>{{ $record->temperature }}</td>
                                <td>{{ $record->pulse }}</td>
                                <td class="noprint">
                                    <a class="btn abtn view" href="{{ route('nursing_records.show', $record) }}"><i
                                            class="fa-solid fa-eye"></i> বিস্তারিত</a>
                                    @if (\App\Support\Registry::can('nursing_records', true))
                                        <a class="btn abtn edit" href="{{ route('nursing_records.edit', $record) }}"><i
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
