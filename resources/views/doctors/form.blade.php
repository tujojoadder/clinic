@extends('layouts.app', ['title' => 'ডাক্তার'])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>
        <div class="panelbody">
            <form method="post" action="{{ $record->exists ? route('doctors.update', $record) : route('doctors.store') }}">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">
                    <div>
                        <label for="name">নাম *</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $record->name) }}" required>
                    </div>
                    <div>
                        <label for="specialty_id">বিভাগ *</label>
                        <div class="inputgroup">
                            <select id="specialty_id" name="specialty_id" required>
                                <option value="">নির্বাচন করুন</option>
                                @foreach ($specialties as $s)
                                    <option value="{{ $s->id }}" @selected((string) old('specialty_id', $record->specialty_id) === (string) $s->id)>{{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if (\App\Support\Registry::can('specialties', true))
                                <button type="button" class="qadd" data-quick-open="quickSpecialty" title="নতুন বিভাগ"><i
                                        class="fa-solid fa-plus"></i></button>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label for="phone">মোবাইল</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $record->phone) }}">
                    </div>
                    <div>
                        <label for="fee">ভিজিট ফি *</label>
                        <input id="fee" name="fee" type="number" min="0" step="0.01"
                            value="{{ old('fee', $record->fee) }}" required>
                    </div>
                    <div>
                        <label for="followup_fee">ফলোআপ ফি *</label>
                        <input id="followup_fee" name="followup_fee" type="number" min="0" step="0.01"
                            value="{{ old('followup_fee', $record->followup_fee) }}" required>
                    </div>
                    @php
                        $selectedDays = collect(old('days', $record->exists ? $record->days->pluck('id')->all() : []))
                            ->map(fn($v) => (int) $v)
                            ->all();
                    @endphp
                    <div class="daysfield">
                        <label>কোন দিন বসেন</label>
                        <div class="daychecks">
                            @foreach ($days as $day)
                                <label class="daycheck">
                                    <input type="checkbox" name="days[]" value="{{ $day->id }}"
                                        @checked(in_array($day->id, $selectedDays))>
                                    {{ $day->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label for="start_time">শুরুর সময়</label>
                        <input id="start_time" name="start_time" type="time"
                            value="{{ old('start_time', $record->start_time ? substr($record->start_time, 0, 5) : '') }}">
                    </div>
                    <div>
                        <label for="end_time">শেষ সময়</label>
                        <input id="end_time" name="end_time" type="time"
                            value="{{ old('end_time', $record->end_time ? substr($record->end_time, 0, 5) : '') }}">
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
                    <a class="btn" href="{{ route('doctors.index') }}">তালিকা</a>
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>

    @if (\App\Support\Registry::can('specialties', true))
        @include('quick.specialty')
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/quick-add.js') }}?v=1"></script>
@endpush
