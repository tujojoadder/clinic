@extends('layouts.app', ['title' => 'ডাক্তার · #' . $record->id])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>ডাক্তার · #{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            <div class="detailgrid">
                <div><label>নাম</label><strong>{{ $record->name }}</strong></div>
                <div><label>বিভাগ</label><strong>{{ $record->specialty?->name }}</strong></div>
                <div><label>মোবাইল</label><strong>{{ $record->phone }}</strong></div>
                <div><label>ভিজিট ফি</label><strong>{{ $record->fee }}</strong></div>
                <div><label>ফলোআপ ফি</label><strong>{{ $record->followup_fee }}</strong></div>
                <div><label>কোন দিন বসেন</label><strong>{{ $record->days->pluck('name')->implode(', ') ?: '—' }}</strong>
                </div>
                <div><label>শুরুর সময়</label><strong>{{ $record->start_time }}</strong></div>
                <div><label>শেষ সময়</label><strong>{{ $record->end_time }}</strong></div>
                <div><label>সক্রিয়</label><strong>{{ $record->active ? 'হ্যাঁ' : 'না' }}</strong></div>
            </div>
            <div class="actions noprint">
                <a class="btn" href="{{ route('doctors.index') }}">তালিকা</a>
                @if (\App\Support\Registry::can('doctors', true))
                    <a class="btn" href="{{ route('doctors.edit', $record) }}">সম্পাদনা</a>
                    <form method="post" action="{{ route('doctors.destroy', $record) }}" data-confirm="রেকর্ড মুছবেন?">
                        @csrf @method('DELETE')
                        <button class="btn danger">ডিলেট</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
