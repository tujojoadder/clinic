@extends('layouts.app', ['title' => 'টেস্ট ও ল্যাব'])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>নতুন এন্ট্রি</h2>
        </div>
        <div class="panelbody">
            <form method="post" action="{{ route('lab_orders.store') }}">
                @csrf
                <div class="formgrid">
                    <div>
                        <label for="patient_id">রোগী *</label>
                        <select id="patient_id" name="patient_id" required>
                            @foreach (\App\Support\Registry::options('patients') as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('patient_id') === (string) $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="admission_id">ভর্তি নম্বর (ঐচ্ছিক)</label>
                        <select id="admission_id" name="admission_id">
                            <option value="">—</option>
                            @foreach ($admissions as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('admission_id') === (string) $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="test_id">টেস্ট *</label>
                        <select id="test_id" name="test_id" required>
                            @foreach ($tests as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('test_id') === (string) $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="ordered_at">অর্ডারের সময় *</label>
                        <input id="ordered_at" name="ordered_at" type="text" class="js-datetime"
                            value="{{ old('ordered_at', now()->format('d-m-Y h:i A')) }}" required>
                    </div>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('lab_orders.index') }}">তালিকা</a>
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>
@endsection
