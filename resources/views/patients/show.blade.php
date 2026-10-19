@extends('layouts.app', ['title' => 'রোগী · #' . $record->id])

@section('heading-action')
    <a class="btn primary" href="{{ route('patients.index') }}"><i class="fa-solid fa-eye"></i> তালিকা</a>
@endsection

@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>রোগী · #{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            @php $genders = ['female' => 'মহিলা', 'male' => 'পুরুষ', 'other' => 'অন্যান্য']; @endphp
            <div class="detailgrid">
                <div><label>রোগীর নাম</label><strong>{{ $record->name }}</strong></div>
                <div><label>জন্মতারিখ</label><strong>{{ date_to_view($record->birth_date) ?: '-' }}</strong></div>
                <div><label>বয়স</label><strong>{{ $record->age_years }}</strong></div>
                <div><label>লিঙ্গ</label><strong>{{ $genders[$record->gender] ?? $record->gender }}</strong></div>
                <div><label>মোবাইল</label><strong>{{ $record->phone }}</strong></div>
                <div><label>ঠিকানা</label><strong>{{ $record->address }}</strong></div>
                <div><label>জরুরি যোগাযোগ</label><strong>{{ $record->emergency_contact }}</strong></div>
            </div>
            @if (\App\Support\Registry::can('patients', true))
                <div class="actions noprint">
                    <a class="btn primary" href="{{ route('patients.edit', $record) }}">সম্পাদনা</a>
                    <form method="post" action="{{ route('patients.destroy', $record) }}" data-confirm="রেকর্ড মুছবেন?">
                        @csrf @method('DELETE')
                        <button class="btn primary">ডিলেট</button>
                    </form>
                </div>
            @endif
        </div>
    </section>
    @include('patients.history')
@endsection
