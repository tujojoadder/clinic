<link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}?v=2">
<aside id="sideBar">
    <div class="brand">
        <span class="logo">+</span>
        <div>Clinic Care<small>CLINIC MANAGEMENT</small>
        </div>
    </div>
    @php
        $current = $module ?? 'dashboard';
        $modules = \App\Support\Registry::modules();
        $linkFor = fn($key) => in_array($key, ['dashboard', 'reports', 'settings', 'discharge'])
            ? route($key)
            : route($key . '.index');
    @endphp
    <nav id="sideNav">
        @foreach (config('clinic.menu') as $i => $entry)
            @if (isset($entry['module']))
                {{-- একক মেনু (যেমন ড্যাশবোর্ড) --}}
                @if (isset($modules[$entry['module']]) && \App\Support\Registry::can($entry['module']))
                    @php $def = $modules[$entry['module']]; @endphp
                    <a class="btn {{ $current === $entry['module'] ? 'primary' : '' }}"
                        href="{{ $linkFor($entry['module']) }}">
                        <span class="navicon"><i class="{{ $def['icon'] }}"></i></span>{{ $def['title'] }}
                    </a>
                @endif
            @else
                {{-- গ্রুপ + সাবমেনু --}}
                @php
                    $items = collect($entry['items'])->filter(
                        fn($key) => isset($modules[$key]) && \App\Support\Registry::can($key),
                    );
                    $hasActive = $items->contains($current);
                @endphp
                @if ($items->isNotEmpty())
                    <div class="navgroup {{ $hasActive ? 'has-active open' : '' }}" data-group="g{{ $i }}">
                        <button type="button" class="navgroup-head"
                            aria-expanded="{{ $hasActive ? 'true' : 'false' }}">
                            <span class="navicon"><i class="{{ $entry['icon'] }}"></i></span>
                            <span class="navtitle">{{ $entry['title'] }}</span>
                            <i class="fa-solid fa-chevron-down chev"></i>
                        </button>
                        <div class="navsub">
                            <div class="navsub-inner">
                                <div class="navsub-list">
                                    @foreach ($items as $key)
                                        <a class="btn {{ $current === $key ? 'primary' : '' }}"
                                            href="{{ $linkFor($key) }}">
                                            <span class="navicon"><i
                                                    class="{{ $modules[$key]['icon'] }}"></i></span>{{ $modules[$key]['title'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        @endforeach
    </nav>

</aside>

{{-- সাইডবারের স্ক্রল পজিশন ও গ্রুপ খোলা/বন্ধ মনে রাখার স্ক্রিপ্ট (বাইরের ফাইল) --}}
<script src="{{ asset('assets/js/sidebar.js') }}?v=2"></script>
