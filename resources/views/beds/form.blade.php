@extends('layouts.app', ['title' => 'বেড ও কেবিন'])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>
        <div class="panelbody">
            <form method="post" action="{{ $record->exists ? route('beds.update', $record) : route('beds.store') }}">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">
                    <div>
                        <label for="name">বেড / কেবিন নম্বর *</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $record->name) }}" required>
                    </div>
                    <div>
                        <label for="ward">ওয়ার্ড *</label>
                        <input id="ward" name="ward" type="text" value="{{ old('ward', $record->ward) }}"
                            required>
                    </div>
                    <div>
                        <label for="type">ধরন *</label>
                        <select id="type" name="type" required>
                            @foreach (['ward' => 'সাধারণ', 'cabin' => 'কেবিন', 'icu' => 'ICU'] as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('type', $record->type) === (string) $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="daily_rate">প্রতি ২৪ ঘণ্টার ভাড়া *</label>
                        <input id="daily_rate" name="daily_rate" type="number" min="0" step="0.01"
                            value="{{ old('daily_rate', $record->daily_rate) }}" required>
                    </div>
                    <div>
                        <label for="state">অবস্থা *</label>
                        <select id="state" name="state" required>
                            @foreach (['available' => 'খালি', 'cleaning' => 'পরিষ্কার', 'inactive' => 'নিষ্ক্রিয়'] as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('state', $record->state) === (string) $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('beds.index') }}">তালিকা</a>
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>
@endsection
