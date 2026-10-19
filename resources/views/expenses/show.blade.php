@extends('layouts.app', ['title' => 'আয়-ব্যয় · #' . $record->id])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>আয়-ব্যয় · #{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            @php $methods = ['cash' => 'ক্যাশ', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'bank' => 'ব্যাংক']; @endphp
            <div class="detailgrid">
                <div><label>খাত</label><strong>{{ $record->category }}</strong></div>
                <div><label>বিবরণ</label><strong>{{ $record->description }}</strong></div>
                <div><label>পরিমাণ</label><strong>{{ $record->amount }}</strong></div>
                <div><label>পদ্ধতি</label><strong>{{ $methods[$record->method] ?? $record->method }}</strong></div>
                <div><label>তারিখ</label><strong>{{ $record->expense_date }}</strong></div>
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
