@extends('layouts.app', ['title' => 'টেস্ট রিপোর্ট টেমপ্লেট'])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>
        <div class="panelbody">
            <form method="post"
                action="{{ $record->exists ? route('test_parameters.update', $record) : route('test_parameters.store') }}">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">
                    <div>
                        <label for="test_id">টেস্ট *</label>
                        <select class="js-select2" id="test_id" name="test_id" required>
                            @foreach ($tests as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('test_id', $record->test_id) === (string) $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="name">প্যারামিটার *</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $record->name) }}"
                            required>
                    </div>
                    <div>
                        <label for="unit">ইউনিট</label>
                        <input id="unit" name="unit" type="text" value="{{ old('unit', $record->unit) }}">
                    </div>
                    <div>
                        <label for="reference_range">যাচাইকৃত রেফারেন্স রেঞ্জ</label>
                        <input id="reference_range" name="reference_range" type="text"
                            value="{{ old('reference_range', $record->reference_range) }}">
                    </div>
                    <div>
                        <label for="sort_order">ক্রম *</label>
                        <input id="sort_order" name="sort_order" type="number" min="0" step="1"
                            value="{{ old('sort_order', $record->exists ? $record->sort_order : 0) }}" required>
                    </div>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('test_parameters.index') }}">তালিকা</a>
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>
@endsection
