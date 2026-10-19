@extends('layouts.app', ['title' => 'ইউজার'])
@section('heading-action')
    @if (\App\Support\Registry::can('users', true))
        <a class="btn primary" href="{{ route('users.create') }}">+ নতুন এন্ট্রি</a>
    @endif
@endsection
@section('content')
    <section class="panel list">
        <div class="filters">
            <form method="get">
                <div class="sbox {{ request()->filled('q') ? 'has' : '' }}">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input name="q" value="{{ request('q') }}" placeholder="তথ্য দিয়ে খুঁজুন"
                        oninput="this.parentNode.classList.toggle('has', this.value !== '')">
                    <button type="button" class="sclear" title="মুছুন"
                        onclick="var f=this.form;f.q.value='';@if (request()->filled('q')) f.submit();@else this.parentNode.classList.remove('has');f.q.focus(); @endif"><i
                            class="fa-solid fa-xmark"></i></button>
                </div>
                <button class="btn"><i class="fa-solid fa-magnifying-glass"></i> খুঁজুন</button>
            </form>
        </div>
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
        <div class="tablewrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>নাম</th>
                        <th>ইমেইল</th>
                        <th>রোল</th>
                        <th>সক্রিয়</th>
                        <th>অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>#{{ $record->id }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->email }}</td>
                            <td>{{ $roles[$record->role] ?? $record->role }}</td>
                            <td>{{ $record->active ? 'হ্যাঁ' : 'না' }}</td>
                            <td>
                                <a class="btn abtn view" href="{{ route('users.show', $record) }}"><i
                                        class="fa-solid fa-eye"></i> বিস্তারিত</a>
                                @if (\App\Support\Registry::can('users', true))
                                    <a class="btn abtn edit" href="{{ route('users.edit', $record) }}"><i
                                            class="fa-solid fa-pen-to-square"></i> এডিট</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">কোনো তথ্য নেই। নতুন এন্ট্রি করুন।</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <span>মোট {{ $records->total() }} রেকর্ড</span>
            @if ($records->previousPageUrl())
                <a class="btn" href="{{ $records->previousPageUrl() }}">← আগের</a>
            @endif
            @if ($records->nextPageUrl())
                <a class="btn" href="{{ $records->nextPageUrl() }}">পরের →</a>
            @endif
        </div>
    </section>
@endsection
