@extends('layouts.app', ['title' => 'বিভাগ (Specialty)'])

@php $canWrite = \App\Support\Registry::can('specialties', true); @endphp

@section('heading-action')
    @if ($canWrite)
        <button type="button" class="btn primary" data-spec-add>+ নতুন বিভাগ</button>
    @endif
@endsection

@section('content')
    <x-list-print title="বিভাগের তালিকা" :paginator="$records">
        <section class="panel list">
            <div class="filters">
                <form method="get" id="specFilter">
                    @if (request()->filled('per_page'))
                        <input type="hidden" name="per_page" value="{{ per_page() }}">
                    @endif
                    <div class="sbox {{ request()->filled('q') ? 'has' : '' }}">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input name="q" value="{{ request('q') }}" placeholder="নাম দিয়ে খুঁজুন">
                    </div>
                    <button class="btn"><i class="fa-solid fa-magnifying-glass"></i> খুঁজুন</button>
                </form>
                <div class="filters-right">
                    <x-per-page :paginator="$records" />
                    <x-print-button />
                </div>
            </div>
            <div class="tablewrap">
                <table id="specTable" data-base="{{ route('specialties.index') }}"
                    data-can-write="{{ $canWrite ? '1' : '0' }}">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>বিভাগের নাম</th>
                            <th>ডাক্তার</th>
                            <th class="noprint">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr data-id="{{ $record->id }}">
                                <td>#{{ $record->id }}</td>
                                <td class="sp-name">{{ $record->name }}</td>
                                <td class="sp-count">{{ $record->doctors_count }}</td>
                                <td class="noprint">
                                    @if ($canWrite)
                                        <button type="button" class="btn abtn edit" data-spec-edit><i
                                                class="fa-solid fa-pen-to-square"></i> এডিট</button>
                                        <button type="button" class="btn abtn danger" data-spec-del><i
                                                class="fa-solid fa-trash"></i> ডিলেট</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="sp-empty">
                                <td colspan="4">কোনো তথ্য নেই। নতুন বিভাগ যোগ করুন।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination :paginator="$records" />
        </section>
    </x-list-print>

    @if ($canWrite)
        <dialog id="specDialog" data-quick-modal>
            <div class="modalhead">
                <h2 id="specTitle">নতুন বিভাগ</h2>
                <button type="button" class="close" data-quick-close aria-label="বন্ধ"></button>
            </div>
            <form id="specForm" method="post" action="{{ route('specialties.index') }}">
                @csrf
                <div class="panelbody">
                    <div class="error qa-errors" hidden></div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="sp_name" class="form-label">বিভাগের নাম *</label>
                            <input id="sp_name" name="name" type="text" maxlength="150" class="form-control w-100"
                                required>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="button" class="btn" data-quick-close>বাতিল</button>
                        <button class="btn primary">সংরক্ষণ করুন</button>
                    </div>
                </div>
            </form>
        </dialog>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/quick-add.js') }}?v=1"></script>
    <script src="{{ asset('assets/js/specialties.js') }}?v=1"></script>
@endpush
