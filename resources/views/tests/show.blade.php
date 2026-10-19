@extends('layouts.app', ['title' => 'টেস্ট ও মূল্য · #' . $record->id])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>টেস্ট ও মূল্য · #{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            <div class="detailgrid">
                <div><label>টেস্টের নাম</label><strong>{{ $record->name }}</strong></div>
                <div><label>নমুনা</label><strong>{{ $record->sample_type }}</strong></div>
                <div><label>মূল্য</label><strong>{{ $record->price }}</strong></div>
                <div><label>সময় (ঘণ্টা)</label><strong>{{ $record->turnaround_hours }}</strong></div>
                <div><label>সক্রিয়</label><strong>{{ $record->active ? 'হ্যাঁ' : 'না' }}</strong></div>
            </div>
            <div class="actions noprint">
                <a class="btn" href="{{ route('tests.index') }}">তালিকা</a>
                @if (\App\Support\Registry::can('tests', true))
                    <a class="btn" href="{{ route('tests.edit', $record) }}">সম্পাদনা</a>
                    <form method="post" action="{{ route('tests.destroy', $record) }}" data-confirm="রেকর্ড মুছবেন?">
                        @csrf @method('DELETE')
                        <button class="btn danger">ডিলেট</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
