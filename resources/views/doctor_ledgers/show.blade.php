@extends('layouts.app', ['title' => 'ডাক্তারের হিসাব · #' . $record->id])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>ডাক্তারের হিসাব · #{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            @php $types = ['payable' => 'প্রাপ্য', 'settlement' => 'পরিশোধ']; @endphp
            <div class="detailgrid">
                <div><label>ডাক্তার</label><strong>{{ $record->doctor?->name ?? '#' . $record->doctor_id }}</strong></div>
                <div><label>এন্ট্রি</label><strong>{{ $types[$record->type] ?? $record->type }}</strong></div>
                <div><label>পরিমাণ</label><strong>{{ $record->amount }}</strong></div>
                <div><label>তারিখ</label><strong>{{ $record->entry_date }}</strong></div>
                <div><label>বিবরণ</label><strong>{{ $record->notes }}</strong></div>
            </div>
            <div class="actions noprint">
                <a class="btn" href="{{ route('doctor_ledgers.index') }}">তালিকা</a>
                @if (\App\Support\Registry::can('doctor_ledgers', true))
                    <a class="btn" href="{{ route('doctor_ledgers.edit', $record) }}">সম্পাদনা</a>
                    <form method="post" action="{{ route('doctor_ledgers.destroy', $record) }}"
                        data-confirm="রেকর্ড মুছবেন?">
                        @csrf @method('DELETE')
                        <button class="btn danger">ডিলেট</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
