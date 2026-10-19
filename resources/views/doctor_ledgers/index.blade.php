@extends('layouts.app', ['title' => 'ডাক্তারের হিসাব'])
@section('heading-action')
    @if (\App\Support\Registry::can('doctor_ledgers', true))
        <a class="btn primary" href="{{ route('doctor_ledgers.create') }}">+ নতুন এন্ট্রি</a>
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
        @php $types = ['payable' => 'প্রাপ্য', 'settlement' => 'পরিশোধ']; @endphp
        <div class="tablewrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ডাক্তার</th>
                        <th>এন্ট্রি</th>
                        <th>পরিমাণ</th>
                        <th>তারিখ</th>
                        <th>বিবরণ</th>
                        <th>অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>#{{ $record->id }}</td>
                            <td>{{ $record->doctor?->name ?? '#' . $record->doctor_id }}</td>
                            <td>{{ $types[$record->type] ?? $record->type }}</td>
                            <td>{{ $record->amount }}</td>
                            <td>{{ $record->entry_date }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($record->notes, 45) }}</td>
                            <td>
                                <a class="btn abtn view" href="{{ route('doctor_ledgers.show', $record) }}"><i
                                        class="fa-solid fa-eye"></i> বিস্তারিত</a>
                                @if (\App\Support\Registry::can('doctor_ledgers', true))
                                    <a class="btn abtn edit" href="{{ route('doctor_ledgers.edit', $record) }}"><i
                                            class="fa-solid fa-pen-to-square"></i> এডিট</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">কোনো তথ্য নেই। নতুন এন্ট্রি করুন।</td>
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
