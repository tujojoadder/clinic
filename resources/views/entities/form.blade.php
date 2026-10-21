@extends('layouts.app', ['title' => $definition['title']])
@section('content')
    <section class="panel">
        <div class="panelhead">
            <h2>{{ $record->exists ? 'তথ্য সম্পাদনা' : 'নতুন এন্ট্রি' }}</h2>
        </div>
        <div class="panelbody">
            <form method="post"
                action="{{ $record->exists ? route('entities.update', [$module, $record->id]) : route('entities.store', $module) }}">
                @csrf

                @if ($record->exists)
                    @method('PUT')
                @endif
                <div class="formgrid">

                    @foreach ($definition['fields'] as $name => $field)
                        @include('components.field', [
                            'name' => $name,
                            'field' => $field,
                            'value' => $record->$name,
                        ])
                    @endforeach

                    @if ($module === 'admissions' && !$record->exists)
                        @include('components.field', [
                            'name' => 'bed_id',
                            'field' => ['বেড', 'fk', true, 'beds'],
                            'value' => null,
                        ])

                        @include('components.field', [
                            'name' => 'advance',
                            'field' => ['অগ্রিম', 'number', false],
                            'value' => 0,
                        ])
                    @endif
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('entities.index', $module) }}">তালিকা</a>
                    <button class="btn primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </section>
@endsection
