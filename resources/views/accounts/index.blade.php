@extends('layouts.app', ['title' => 'অ্যাকাউন্ট'])

@php $canWrite = \App\Support\Registry::can('accounts', true); @endphp

@section('heading-action')
    @if ($canWrite)
        <button type="button" class="btn primary" data-acc-add>+ নতুন অ্যাকাউন্ট</button>
    @endif
@endsection

@section('content')
    <x-list-print title="অ্যাকাউন্টের তালিকা" :paginator="$records">
        <section class="panel list">
            <div class="filters">
                <form method="get" id="accFilter">
                    @if (request()->filled('per_page'))
                        <input type="hidden" name="per_page" value="{{ per_page() }}">
                    @endif
                    <div class="sbox {{ request()->filled('q') ? 'has' : '' }}">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input name="q" value="{{ request('q') }}" placeholder="নাম / নম্বর দিয়ে খুঁজুন">
                    </div>
                    <button class="btn"><i class="fa-solid fa-magnifying-glass"></i> খুঁজুন</button>
                </form>
                <div class="filters-right">
                    <x-per-page :paginator="$records" />
                    <x-print-button />
                </div>
            </div>
            <div class="tablewrap">
                <table id="accTable" data-base="{{ route('accounts.index') }}">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>অ্যাকাউন্টের নাম</th>
                            <th>হোল্ডারের নাম</th>
                            <th>অ্যাকাউন্ট নম্বর</th>
                            <th>ডিফল্ট</th>
                            <th class="noprint">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr data-id="{{ $record->id }}" data-default="{{ $record->is_default ? 1 : 0 }}">
                                <td>#{{ $record->id }}</td>
                                <td class="ac-name">{{ $record->name }}</td>
                                <td class="ac-holder">{{ $record->holder_name }}</td>
                                <td class="ac-number">{{ $record->account_number }}</td>
                                <td class="ac-default">
                                    @if ($record->is_default)
                                        <span class="badge">ডিফল্ট</span>
                                    @endif
                                </td>
                                <td class="noprint">
                                    @if ($canWrite)
                                        <button type="button" class="btn abtn edit" data-acc-edit><i
                                                class="fa-solid fa-pen-to-square"></i> এডিট</button>
                                        <button type="button" class="btn abtn danger" data-acc-del><i
                                                class="fa-solid fa-trash"></i> ডিলেট</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="ac-empty">
                                <td colspan="6">কোনো তথ্য নেই। নতুন অ্যাকাউন্ট যোগ করুন।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination :paginator="$records" />
        </section>
    </x-list-print>

    @if ($canWrite)
        <dialog id="accDialog" data-quick-modal>
            <div class="modalhead">
                <h2 id="accTitle">নতুন অ্যাকাউন্ট</h2>
                <button type="button" class="close" data-quick-close aria-label="বন্ধ"></button>
            </div>
            <form id="accForm" method="post" action="{{ route('accounts.index') }}">
                @csrf
                <div class="panelbody">
                    <div class="error qa-errors" hidden></div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="ac_name" class="form-label">অ্যাকাউন্টের নাম *</label>
                            <input id="ac_name" name="name" type="text" maxlength="150" class="form-control w-100"
                                required>
                        </div>
                        <div class="col-12">
                            <label for="ac_holder" class="form-label">হোল্ডারের নাম *</label>
                            <input id="ac_holder" name="holder_name" type="text" maxlength="150"
                                class="form-control w-100" required>
                        </div>
                        <div class="col-12">
                            <label for="ac_number" class="form-label">অ্যাকাউন্ট নম্বর *</label>
                            <input id="ac_number" name="account_number" type="text" maxlength="50"
                                class="form-control w-100" required>
                        </div>
                        <div class="col-12">
                            <input type="hidden" name="is_default" value="0">
                            <label>
                                <input id="ac_default" name="is_default" type="checkbox" value="1">
                                ডিফল্ট অ্যাকাউন্ট হিসেবে সেট করুন
                            </label>
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
    <script src="{{ asset('assets/js/accounts.js') }}?v=1"></script>
@endpush
