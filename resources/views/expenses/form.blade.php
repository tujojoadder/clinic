@extends('layouts.app', ['title' => 'খরচ'])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>
        <div class="panelbody">
            <form method="post" action="{{ $record->exists ? route('expenses.update', $record) : route('expenses.store') }}">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">
                    <div>
                        <label for="category">খাত *</label>
                        <input id="category" name="category" type="text" value="{{ old('category', $record->category) }}"
                            required>
                    </div>
                    <div>
                        <label for="description">বিবরণ *</label>
                        <input id="description" name="description" type="text"
                            value="{{ old('description', $record->description) }}" required>
                    </div>
                    <div>
                        <label for="amount">পরিমাণ *</label>
                        <input id="amount" name="amount" type="number" min="0.01" step="0.01"
                            value="{{ old('amount', $record->amount) }}" required>
                    </div>
                    <div>
                        <label for="method">পদ্ধতি *</label>
                        <select id="method" name="method" required>
                            @foreach (['cash' => 'ক্যাশ', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'bank' => 'ব্যাংক'] as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('method', $record->method) === (string) $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="expense_date">তারিখ *</label>
                        <input id="expense_date" name="expense_date" type="text" class="js-date" autocomplete="off"
                            placeholder="dd-mm-yyyy"
                            value="{{ date_to_view(old('expense_date', $record->expense_date ?: now()->format('Y-m-d'))) }}"
                            required>
                    </div>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('expenses.index') }}">তালিকা</a>
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>
@endsection
