@extends('layouts.app', ['title' => 'ডাক্তারের হিসাব'])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>
        <div class="panelbody">
            <form method="post"
                action="{{ $record->exists ? route('doctor_ledgers.update', $record) : route('doctor_ledgers.store') }}">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">
                    <div>
                        <label for="doctor_id">ডাক্তার *</label>
                        <select id="doctor_id" name="doctor_id" required>
                            @foreach (\App\Support\Registry::options('doctors') as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('doctor_id', $record->doctor_id) === (string) $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="type">এন্ট্রি *</label>
                        <select id="type" name="type" required>
                            @foreach (['payable' => 'প্রাপ্য', 'settlement' => 'পরিশোধ'] as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('type', $record->type) === (string) $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="amount">পরিমাণ *</label>
                        <input id="amount" name="amount" type="number" min="0.01" step="0.01"
                            value="{{ old('amount', $record->amount) }}" required>
                    </div>
                    <div>
                        <label for="entry_date">তারিখ *</label>
                        <input id="entry_date" name="entry_date" type="date"
                            value="{{ old('entry_date', $record->entry_date ?: now()->format('Y-m-d')) }}" required>
                    </div>
                    <div>
                        <label for="notes">বিবরণ *</label>
                        <input id="notes" name="notes" type="text" value="{{ old('notes', $record->notes) }}"
                            required>
                    </div>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('doctor_ledgers.index') }}">তালিকা</a>
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>
@endsection
