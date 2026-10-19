@extends('layouts.app', ['title' => 'টেস্ট ও মূল্য'])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>
        <div class="panelbody">
            <form method="post" action="{{ $record->exists ? route('tests.update', $record) : route('tests.store') }}">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">
                    <div>
                        <label for="name">টেস্টের নাম *</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $record->name) }}" required>
                    </div>
                    <div>
                        <label for="sample_type">নমুনা *</label>
                        <input id="sample_type" name="sample_type" type="text"
                            value="{{ old('sample_type', $record->sample_type) }}" required>
                    </div>
                    <div>
                        <label for="price">মূল্য *</label>
                        <input id="price" name="price" type="number" min="0" step="0.01"
                            value="{{ old('price', $record->price) }}" required>
                    </div>
                    <div>
                        <label for="turnaround_hours">সময় (ঘণ্টা) *</label>
                        <input id="turnaround_hours" name="turnaround_hours" type="number" min="0" step="1"
                            value="{{ old('turnaround_hours', $record->exists ? $record->turnaround_hours : 24) }}"
                            required>
                    </div>
                    <div>
                        <label for="active">সক্রিয় *</label>
                        <select id="active" name="active" required>
                            @foreach (['1' => 'হ্যাঁ', '0' => 'না'] as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('active', $record->exists ? (int) $record->active : 1) === (string) $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('tests.index') }}">তালিকা</a>
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>
@endsection
