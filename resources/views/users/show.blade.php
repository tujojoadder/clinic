@extends('layouts.app', ['title' => 'ইউজার · #' . $record->id])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>ইউজার · #{{ $record->id }}</h2>
        </div>
        <div class="panelbody">
            @php
                $roles = [
                    'admin' => 'অ্যাডমিন',
                    'reception' => 'রিসেপশন',
                    'doctor' => 'ডাক্তার',
                    'nurse' => 'নার্স',
                    'lab' => 'ল্যাব',
                    'cashier' => 'ক্যাশিয়ার',
                ];
            @endphp
            <div class="detailgrid">
                <div><label>নাম</label><strong>{{ $record->name }}</strong></div>
                <div><label>ইমেইল</label><strong>{{ $record->email }}</strong></div>
                <div><label>রোল</label><strong>{{ $roles[$record->role] ?? $record->role }}</strong></div>
                <div><label>সক্রিয়</label><strong>{{ $record->active ? 'হ্যাঁ' : 'না' }}</strong></div>
            </div>
            <div class="actions noprint">
                <a class="btn" href="{{ route('users.index') }}">তালিকা</a>
                @if (\App\Support\Registry::can('users', true))
                    <a class="btn" href="{{ route('users.edit', $record) }}">সম্পাদনা</a>
                    @if ($record->id !== auth()->id())
                        <form method="post" action="{{ route('users.destroy', $record) }}" data-confirm="রেকর্ড মুছবেন?">
                            @csrf @method('DELETE')
                            <button class="btn danger">ডিলেট</button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </section>
@endsection
