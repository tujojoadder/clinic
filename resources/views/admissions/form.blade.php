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
                            <select class="js-select2" id="patient_id" name="patient_id" required>
                                @foreach (\App\Support\Registry::options('patients') as $key => $label)
                                    <option value="{{ $key }}" @selected((string) old('patient_id') === (string) $key)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="admitted_at">ভর্তির তারিখ ও সময় *</label>
                            <input id="admitted_at" name="admitted_at" type="text" class="js-datetime"
                                value="{{ datetime_to_view(old('admitted_at', now())) }}" required>
                        </div>
                    @endif
                    <div>
                        <label for="doctor_id">ডাক্তার *</label>
                        <select class="js-select2" id="doctor_id" name="doctor_id" required>
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
                            <select class="js-select2" id="bed_id" name="bed_id" required>
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
                            <label for="account_id">অ্যাকাউন্ট *</label>
                            <select class="js-select2" id="account_id" name="account_id" required>
                                @foreach (account_options() as $a)
                                    <option value="{{ $a->id }}" @selected((string) old('account_id', $a->is_default ? $a->id : '') === (string) $a->id)>
                                        {{ $a->name }}{{ $a->account_number ? ' — ' . $a->account_number : '' }}
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
