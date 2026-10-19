@extends('layouts.app', ['title' => 'রোগী ভর্তি'])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>
        <div class="panelbody">
            <form method="post"
                action="{{ $record->exists ? route('admissions.update', $record) : route('admissions.store') }}">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">
                    @if ($record->exists)
                        <div>
                            <label>রোগী</label>
                            <input type="text" value="{{ $record->patient?->name }}" readonly>
                        </div>
                        <div>
                            <label>ভর্তির তারিখ ও সময়</label>
                            <input type="text" value="{{ $record->admitted_at }}" readonly>
                        </div>
                    @else
                        <div>
                            <label for="patient_id">রোগী *</label>
                            <select id="patient_id" name="patient_id" required>
                                @foreach (\App\Support\Registry::options('patients') as $key => $label)
                                    <option value="{{ $key }}" @selected((string) old('patient_id') === (string) $key)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="admitted_at">ভর্তির তারিখ ও সময় *</label>
                            <input id="admitted_at" name="admitted_at" type="datetime-local"
                                value="{{ old('admitted_at', now()->format('Y-m-d\TH:i')) }}" required>
                        </div>
                    @endif
                    <div>
                        <label for="doctor_id">ডাক্তার *</label>
                        <select id="doctor_id" name="doctor_id" required>
                            @foreach (\App\Support\Registry::options('doctors') as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('doctor_id', $record->doctor_id) === (string) $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="reason">ভর্তির কারণ *</label>
                        <textarea id="reason" name="reason" required>{{ old('reason', $record->reason) }}</textarea>
                    </div>
                    @unless ($record->exists)
                        <div>
                            <label for="bed_id">বেড *</label>
                            <select id="bed_id" name="bed_id" required>
                                @foreach ($beds as $key => $label)
                                    <option value="{{ $key }}" @selected((string) old('bed_id') === (string) $key)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="advance">অগ্রিম</label>
                            <input id="advance" name="advance" type="number" min="0" step="0.01"
                                value="{{ old('advance', 0) }}">
                        </div>
                        <div>
                            <label for="method">পদ্ধতি *</label>
                            <select id="method" name="method" required>
                                @foreach (['cash' => 'ক্যাশ', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'bank' => 'ব্যাংক'] as $key => $label)
                                    <option value="{{ $key }}" @selected((string) old('method', 'cash') === (string) $key)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endunless
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('admissions.index') }}">তালিকা</a>
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>
@endsection
