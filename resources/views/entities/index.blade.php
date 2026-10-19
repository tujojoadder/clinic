@extends('layouts.app', ['title' => $definition['title']])
@section('heading-action')
    @if (\App\Support\Registry::can($module, true))
        <a class="btn primary" href="{{ route('entities.create', $module) }}">
            + নতুন এন্ট্রি</a>
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
        @php $columns = array_slice(array_filter($definition['fields'], fn($key) => $key !== 'password', ARRAY_FILTER_USE_KEY), 0, 5); @endphp
        <div class="tablewrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        @foreach ($columns as $field)
                            <th>{{ $field[0] }}</th>
                        @endforeach
                        <th>
                            অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse($records as $record)
                        <tr>
                            <td>#{{ $record->id }}</td>

                            @foreach ($columns as $key => $field)
                                <td>{{ \Illuminate\Support\Str::limit(\App\Support\Format::value($record->$key, $field), 45) }}
                                </td>
                            @endforeach
                            <td>

                                @if ($record->status)
                                    <span class="badge s-{{ $record->status }}">{{ $record->status }}</span>
                                @endif
                                <a class="btn abtn view" href="{{ route('entities.show', [$module, $record->id]) }}"><i
                                        class="fa-solid fa-eye"></i> বিস্তারিত</a>

                                @if (\App\Support\Registry::can($module, true) && $module !== 'lab_orders')
                                    <a class="btn abtn edit" href="{{ route('entities.edit', [$module, $record->id]) }}"><i
                                            class="fa-solid fa-pen-to-square"></i> এডিট</a>
                                @endif
                            </td>
                        </tr>

                    @empty<tr>
                            <td colspan="8">কোনো তথ্য নেই। নতুন এন্ট্রি করুন।</td>
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
