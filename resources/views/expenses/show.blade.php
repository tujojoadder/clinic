@extends('layouts.app', ['title' => 'খরচ · #' . $record->id])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>খরচ · #{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            <div class="detailgrid">
                <div><label>খাত</label><strong>{{ $record->category }}</strong></div>
                <div><label>বিবরণ</label><strong>{{ $record->description }}</strong></div>
                <div><label>পরিমাণ</label><strong>{{ $record->amount }}</strong></div>
                <div><label>অ্যাকাউন্ট</label><strong>{{ $record->account?->name }}</strong></div>
                <div><label>তারিখ</label><strong>{{ date_to_view($record->expense_date) }}</strong></div>
            </div>
            <div class="actions noprint">
                <a class="btn" href="{{ route('expenses.index') }}">তালিকা</a>
                @if (\App\Support\Registry::can('expenses', true))
                    <a class="btn" href="{{ route('expenses.edit', $record) }}">সম্পাদনা</a>
                    <form method="post" action="{{ route('expenses.destroy', $record) }}" data-confirm="রেকর্ড মুছবেন?">
                        @csrf @method('DELETE')
                        <button class="btn danger">ডিলেট</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
