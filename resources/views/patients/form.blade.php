@extends('layouts.app', ['title' => $record->exists ? 'রোগী · #' . $record->id : 'নতুন রোগী'])

@section('heading-action')
    <a class="btn primary" href="{{ route('patients.index') }}"><i class="fa-solid fa-eye"></i> তালিকা</a>
@endsection

@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>
        <div class="panelbody">
            <form method="post" action="{{ $record->exists ? route('patients.update', $record) : route('patients.store') }}">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">
                    <div>
                        <label for="name">রোগীর নাম *</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $record->name) }}"
                            required>
                    </div>
                    <div>
                        <label for="birth_date">জন্মতারিখ</label>
                        @php $bd = old('birth_date', $record->birth_date); @endphp
                        <input id="birth_date" name="birth_date" type="text" class="js-date" autocomplete="off"
                            value="{{ $bd ? \Carbon\Carbon::parse($bd)->format('d-m-Y') : '' }}">
                    </div>
                    <div>
                        <label for="age_years">বয়স (জন্মতারিখ না থাকলে)</label>
                        <input id="age_years" name="age_years" type="number" min="0" step="1"
                            value="{{ old('age_years', $record->age_years) }}">
                    </div>
                    <div>
                        <label for="gender">লিঙ্গ *</label>
                        <select id="gender" name="gender" required>
                            @foreach (['female' => 'মহিলা', 'male' => 'পুরুষ', 'other' => 'অন্যান্য'] as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('gender', $record->gender) === (string) $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="phone">মোবাইল *</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $record->phone) }}"
                            required>
                    </div>
                    <div>
                        <label for="emergency_contact">জরুরি যোগাযোগ</label>
                        <input id="emergency_contact" name="emergency_contact" type="text"
                            value="{{ old('emergency_contact', $record->emergency_contact) }}">
                    </div>
                    <div>
                        <label for="reference_id">রেফারেন্স</label>
                        <div class="inputgroup">
                            <select id="reference_id" name="reference_id"
                                data-ajax-url="{{ route('ajax.references.search') }}"
                                data-placeholder="রেফারেন্স নির্বাচন করুন">
                                <option></option>
                                @if ($rid = old('reference_id', $record->reference_id))
                                    <option value="{{ $rid }}" selected>
                                        {{ ($m = \App\Models\ReferencePerson::find($rid)) ? $m->name . ($m->phone ? ' (' . $m->phone . ')' : '') : '' }}
                                    </option>
                                @endif
                            </select>
                            @if (\App\Support\Registry::can('references', true))
                                <button type="button" class="qadd" data-quick-open="quickReference"
                                    title="নতুন রেফারেন্স"><i class="fa-solid fa-plus"></i></button>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label for="address">ঠিকানা</label>
                        <textarea id="address" name="address">{{ old('address', $record->address) }}</textarea>
                    </div>
                </div>
                <div class="actions">
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>
    @if (\App\Support\Registry::can('references', true))
        @include('quick.reference')
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/quick-add.js') }}?v=1"></script>
@endpush
