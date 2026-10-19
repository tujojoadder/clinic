@extends('layouts.app', ['title' => 'বিলিং ও পেমেন্ট'])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>
        <div class="panelbody">
            <form method="post" action="{{ $record->exists ? route('invoices.update', $record) : route('invoices.store') }}">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">
                    <div>
                        <label for="patient_id">রোগী *</label>
                        <select id="patient_id" name="patient_id" required>
                            @foreach (\App\Support\Registry::options('patients') as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('patient_id', $record->patient_id) === (string) $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="discount">ছাড় *</label>
                        <input id="discount" name="discount" type="number" min="0" step="0.01"
                            value="{{ old('discount', $record->exists ? $record->discount : 0) }}" required>
                    </div>
                    <div>
                        <label for="notes">বিলের নোট</label>
                        <textarea id="notes" name="notes">{{ old('notes', $record->notes) }}</textarea>
                    </div>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('invoices.index') }}">তালিকা</a>
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>
@endsection
