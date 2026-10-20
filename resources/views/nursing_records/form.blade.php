@extends('layouts.app', ['title' => 'নার্সিং রেকর্ড'])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>
        <div class="panelbody">
            <form method="post"
                action="{{ $record->exists ? route('nursing_records.update', $record) : route('nursing_records.store') }}">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">
                    <div>
                        <label for="admission_id">ভর্তি নম্বর *</label>
                        <select id="admission_id" name="admission_id" required>
                            @foreach ($admissions as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('admission_id', $record->admission_id) === (string) $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="recorded_at">পর্যবেক্ষণের সময় *</label>
                        <input id="recorded_at" name="recorded_at" type="text" class="js-datetime"
                            value="{{ datetime_to_view(old('recorded_at', $record->recorded_at ?: now())) }}" required>
                    </div>
                    <div>
                        <label for="blood_pressure">রক্তচাপ (mmHg)</label>
                        <input id="blood_pressure" name="blood_pressure" type="text"
                            value="{{ old('blood_pressure', $record->blood_pressure) }}">
                    </div>
                    <div>
                        <label for="temperature">তাপমাত্রা (°F)</label>
                        <input id="temperature" name="temperature" type="number" min="0" step="0.01"
                            value="{{ old('temperature', $record->temperature) }}">
                    </div>
                    <div>
                        <label for="pulse">পালস</label>
                        <input id="pulse" name="pulse" type="number" min="0" step="1"
                            value="{{ old('pulse', $record->pulse) }}">
                    </div>
                    <div>
                        <label for="notes">নার্সিং নোট</label>
                        <textarea id="notes" name="notes">{{ old('notes', $record->notes) }}</textarea>
                    </div>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('nursing_records.index') }}">তালিকা</a>
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>
@endsection
