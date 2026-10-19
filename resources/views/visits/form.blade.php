@extends('layouts.app', ['title' => 'ডাক্তার ও সিরিয়াল'])

@section('heading-action')
    @if (\App\Support\Registry::can('visits', true))
        <a class="btn primary" href="{{ route('visits.index') }}"><i class="fa-solid fa-eye"></i> তালিকা</a>
    @endif
@endsection

@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>

        <div class="panelbody">
            <form id="visitForm" method="post"
                action="{{ $record->exists ? route('visits.update', $record) : route('visits.store') }}"
                data-fee-url="{{ route('ajax.doctors.fees', '__ID__') }}" data-autoload="{{ $record->exists ? '0' : '1' }}">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">
                    <div>
                        <label for="patient_id">রোগী *</label>
                        <div class="inputgroup">
                            <select id="patient_id" name="patient_id" required
                                data-ajax-url="{{ route('ajax.patients.search') }}" data-placeholder="রোগী নির্বাচন করুন">
                                <option></option>
                                @if ($pid = old('patient_id', $record->patient_id))
                                    <option value="{{ $pid }}" selected>
                                        {{ \App\Models\Patient::find($pid)?->name }}</option>
                                @endif
                            </select>
                            @if (\App\Support\Registry::can('patients', true))
                                <button type="button" class="qadd" data-quick-open="quickPatient" title="নতুন রোগী"><i
                                        class="fa-solid fa-plus"></i></button>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label for="doctor_id">ডাক্তার *</label>
                        <div class="inputgroup">
                            <select id="doctor_id" name="doctor_id" required
                                data-ajax-url="{{ route('ajax.doctors.search') }}" data-extra-date-from="#appointment_at"
                                data-placeholder="ডাক্তার নির্বাচন করুন">
                                <option></option>
                                @if ($did = old('doctor_id', $record->doctor_id))
                                    <option value="{{ $did }}" selected>
                                        {{ \App\Models\Doctor::find($did)?->name }}
                                    </option>
                                @endif
                            </select>
                            @if (\App\Support\Registry::can('doctors', true))
                                <button type="button" class="qadd" data-quick-open="quickDoctor" title="নতুন ডাক্তার"><i
                                        class="fa-solid fa-plus"></i></button>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label for="appointment_at">তারিখ ও সময় *</label>
                        <input type="text" id="appointment_at" name="appointment_at" class="js-datetime"
                            autocomplete="off"
                            value="{{ old('appointment_at', optional($record->appointment_at)->format('d-m-Y h:i A')) }}"
                            required>
                    </div>

                    <div>
                        <label for="visit_type">ভিজিট *</label>
                        <select id="visit_type" name="visit_type" required>
                            @foreach (['new' => 'নতুন', 'followup' => 'ফলোআপ'] as $key => $label)
                                <option value="{{ $key }}" @selected((string) old('visit_type', $record->visit_type) === (string) $key)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="fee">ভিজিট ফি *</label>
                        <input id="fee" name="fee" type="number" min="0" step="0.01"
                            value="{{ old('fee', $record->fee) }}" required>
                    </div>

                    @if ($record->exists)
                        {{-- স্ট্যাটাস শুধু এডিটের সময় বদলানো যাবে --}}
                        <div>
                            <label for="status">স্ট্যাটাস *</label>
                            <select id="status" name="status" required>
                                @foreach (['waiting' => 'অপেক্ষমাণ', 'completed' => 'সম্পন্ন', 'cancelled' => 'বাতিল'] as $key => $label)
                                    <option value="{{ $key }}" @selected((string) old('status', $record->status) === (string) $key)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        {{-- নতুন ভিজিট সবসময় অপেক্ষমাণ; VisitRequest এ status required তাই hidden ইনপুট লাগবে --}}
                        <input type="hidden" name="status" value="waiting">
                    @endif

                    <div>
                        <label for="notes">নোট</label>
                        <textarea id="notes" name="notes">{{ old('notes', $record->notes) }}</textarea>
                    </div>
                </div>
                <div class="actions">
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>

    @if (\App\Support\Registry::can('patients', true))
        @include('quick.patient')
    @endif

    @if (\App\Support\Registry::can('doctors', true))
        @include('quick.doctor')
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/quick-add.js') }}?v=1"></script>
    <script src="{{ asset('assets/js/visit-form.js') }}?v=2"></script>
@endpush
