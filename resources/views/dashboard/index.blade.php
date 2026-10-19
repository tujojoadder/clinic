@extends('layouts.app', ['title' => 'ড্যাশবোর্ড', 'module' => 'dashboard'])
@section('content')
    <link rel="stylesheet" href="{{ asset('assets/dashboard.css') }}?v=2">
    @php
        $icons = ['👨‍⚕️', '🛏️', '🩺', '📅', '💰'];
        $statModules = ['patients', 'admissions', 'beds', 'visits', 'invoices'];
    @endphp
    <div class="dash">

        <div class="stats">
            @foreach ($stats as $stat)
                @php
                    $mod = $statModules[$loop->index] ?? null;
                    $url = $mod && \App\Support\Registry::can($mod) ? route('entities.index', $mod) : null;
                @endphp
                <a class="stat c{{ $loop->index % 5 }} {{ $url ? '' : 'nolink' }}" href="{{ $url ?? 'javascript:void(0)' }}">
                    <span class="sicon">{{ $icons[$loop->index % 5] }}</span>
                    <div class="top">{{ $stat[0] }}</div>
                    <strong>{{ $stat[1] }}</strong>
                    <small>{{ $stat[2] }}</small>
                </a>
            @endforeach
        </div>

        <div class="quick">
            @foreach (['patients' => 'নতুন রোগী', 'visits' => 'সিরিয়াল দিন', 'admissions' => 'রোগী ভর্তি', 'lab_orders' => 'টেস্ট অর্ডার'] as $key => $label)
                @if (\App\Support\Registry::can($key, true))
                    <a class="btn qbtn q-{{ $key }}" href="{{ route('entities.create', $key) }}">＋
                        {{ $label }}</a>
                @endif
            @endforeach
        </div>

        <div class="grid">
            @if (\App\Support\Registry::can('visits'))
                <section class="panel p-visits">
                    <div class="panelhead">
                        <h2>📅 আজকের সিরিয়াল</h2>
                    </div>
                    <div class="tablewrap">
                        <table>
                            <tr>
                                <th>রোগী</th>
                                <th>ডাক্তার</th>
                                <th>সময়</th>
                                <th>স্ট্যাটাস</th>
                            </tr>
                            @foreach ($visits as $visit)
                                <tr>
                                    <td>{{ $visit->patient->name }}</td>
                                    <td>{{ $visit->doctor->name }}</td>
                                    <td>{{ $visit->appointment_at }}</td>
                                    <td>
                                        <span class="badge s-{{ $visit->status }}">{{ $visit->status }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </section>
            @endif

            @if (\App\Support\Registry::can('lab_orders'))
                <section class="panel p-lab">
                    <div class="panelhead">
                        <h2>🧪 ল্যাব কার্যক্রম</h2>
                    </div>
                    <div class="panelbody">
                        @foreach ($lab as $row)
                            <div class="doctor">
                                <span class="badge s-{{ $row->status }}">{{ $row->status }}</span>
                                <span>{{ $row->count }}টি</span>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        @if (\App\Support\Registry::can('admissions'))
            <section class="panel p-adm">
                <div class="panelhead">
                    <h2>🛏️ সাম্প্রতিক ভর্তি</h2>
                </div>
                <div class="panelbody">
                    @foreach ($admissions as $admission)
                        <a class="btn"
                            href="{{ route('entities.show', ['admissions', $admission->id]) }}">{{ $admission->patient->name }}
                            · ADM-{{ $admission->id }}</a>
                    @endforeach
                </div>
            </section>
        @endif

    </div>
@endsection
