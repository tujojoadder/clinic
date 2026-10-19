@extends('layouts.app', ['title' => 'ডাক্তার ও সিরিয়াল'])
@section('heading-action')
    @if (\App\Support\Registry::can('visits', true))
        <a class="btn primary" href="{{ route('visits.create') }}">+ নতুন এন্ট্রি</a>
    @endif
@endsection
@section('content')
    <x-list-print title="ভিজিটের তালিকা" :paginator="$records">
        <section class="panel list">
            <div class="filters">
                <form method="get">
                    {{-- খোঁজার সময় প্রতি পেজে কতটি সেটা যেন হারিয়ে না যায় --}}
                    @if (request()->filled('per_page'))
                        <input type="hidden" name="per_page" value="{{ per_page() }}">
                    @endif
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
                <div class="filters-right">
                    <x-per-page :paginator="$records" />
                    <x-print-button />
                </div>
            </div>
            @php $types = ['new' => 'নতুন', 'followup' => 'ফলোআপ']; @endphp
            <div class="tablewrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>রোগী</th>
                            <th>ডাক্তার</th>
                            <th>তারিখ ও সময়</th>
                            <th>সিরিয়াল নম্বর</th>
                            <th>ভিজিট</th>
                            <th>স্ট্যাটাস</th>
                            <th class="noprint">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>#{{ $record->id }}</td>
                                <td>{{ $record->patient?->name ?? '#' . $record->patient_id }}</td>
                                <td>{{ $record->doctor?->name ?? '#' . $record->doctor_id }}</td>
                                <td>{{ $record->appointment_at?->format('d-m-Y h:i A') }}</td>
                                <td>{{ $record->serial_no }}</td>
                                <td>{{ $types[$record->visit_type] ?? $record->visit_type }}</td>
                                <td><span class="badge s-{{ $record->status }}">{{ $record->status }}</span></td>
                                <td class="noprint">
                                    <a class="btn abtn view" href="{{ route('visits.show', $record) }}"><i
                                            class="fa-solid fa-eye"></i> বিস্তারিত</a>
                                    @if (\App\Support\Registry::can('visits', true))
                                        <a class="btn abtn edit" href="{{ route('visits.edit', $record) }}"><i
                                                class="fa-solid fa-pen-to-square"></i> এডিট</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">কোনো তথ্য নেই। নতুন এন্ট্রি করুন।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination :paginator="$records" />
        </section>
    </x-list-print>
@endsection
