@extends('layouts.app', ['title' => 'রেফারেন্স'])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>
        <div class="panelbody">
            <form method="post"
                action="{{ $record->exists ? route('references.update', $record) : route('references.store') }}">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">
                    <div>
                        <label for="name">নাম *</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $record->name) }}" required>
                    </div>
                    <div>
                        <label for="phone">মোবাইল *</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $record->phone) }}"
                            required>
                    </div>
                    <div>
                        <label for="commission_percent">কমিশন (%) *</label>
                        <input id="commission_percent" name="commission_percent" type="number" min="0"
                            max="100" step="0.01"
                            value="{{ old('commission_percent', $record->commission_percent) }}" required>
                    </div>
                    <div>
                        <label for="joining_date">জয়েনিং ডেট</label>
                        <input id="joining_date" name="joining_date" type="date"
                            value="{{ old('joining_date', $record->joining_date ?: now()->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('references.index') }}">তালিকা</a>
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>
@endsection
