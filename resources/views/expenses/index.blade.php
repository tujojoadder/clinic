@extends('layouts.app', ['title' => 'খরচ'])
@section('heading-action')
    @if (\App\Support\Registry::can('expenses', true))
        <a class="btn primary" href="{{ route('expenses.create') }}">+ নতুন এন্ট্রি</a>
    @endif
@endsection
@section('content')
    <x-list-print title="খরচের তালিকা" :paginator="$records">
        <section class="panel list">
            @php
                // পুরনো এন্ট্রির (account_id নেই) জন্য fallback
                $methods = ['cash' => 'ক্যাশ', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'bank' => 'ব্যাংক'];
                $hasFilter =
                    request()->filled('q') ||
                    request()->filled('expense_category_id') ||
                    request()->filled('account_id');
            @endphp

            <div class="filters">
                {{-- Row 1: সার্চ + প্রতি পেজে কতটি + প্রিন্ট --}}
                <form method="get" id="expenseFilter">
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
                    <select class="js-select2" name="expense_category_id" form="expenseFilter" data-label="খাত"
                        onchange="this.form.submit()">
                        <option value="">সব খাত</option>
                        @foreach (expense_category_options() as $c)
                            <option value="{{ $c->id }}" @selected((string) request('expense_category_id') === (string) $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>

                    <select class="js-select2" name="account_id" form="expenseFilter" data-label="অ্যাকাউন্ট"
                        onchange="this.form.submit()">
                        <option value="">সব অ্যাকাউন্ট</option>
                        @foreach (account_options() as $a)
                            <option value="{{ $a->id }}" @selected((string) request('account_id') === (string) $a->id)>{{ $a->name }}</option>
                        @endforeach
                    </select>

                    <div class="filter-reset">
                        @if ($hasFilter)
                            <a class="btn"
                                href="{{ route('expenses.index', request()->filled('per_page') ? ['per_page' => per_page()] : []) }}">
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
                            <th>খাত</th>
                            <th>বিবরণ</th>
                            <th>পরিমাণ</th>
                            <th>অ্যাকাউন্ট</th>
                            <th>তারিখ</th>
                            <th class="noprint">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>#{{ $record->id }}</td>
                                <td>{{ $record->category?->name }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($record->description, 45) }}</td>
                                <td>{{ $record->amount }}</td>
                                <td>{{ $record->account?->name ?? ($methods[$record->method] ?? $record->method) }}</td>
                                <td>{{ date_to_view($record->expense_date) }}</td>
                                <td class="noprint">
                                    <a class="btn abtn view" href="{{ route('expenses.show', $record) }}"><i
                                            class="fa-solid fa-eye"></i> বিস্তারিত</a>
                                    @if (\App\Support\Registry::can('expenses', true))
                                        <a class="btn abtn edit" href="{{ route('expenses.edit', $record) }}"><i
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
