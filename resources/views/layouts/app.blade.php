<!doctype html>
<html lang="bn">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'ড্যাশবোর্ড' }} · {{ \App\Support\Registry::setting('clinic_name', 'Clinic Care') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/fa/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css">
    <link rel="stylesheet" href="{{ asset('assets/app.css') }}">
</head>

<body>

    @include('partials.sidebar')
    <main>

        @include('partials.topbar')
        <div id="content">
            <div class="printhead hidden">
                <h1>{{ \App\Support\Registry::setting('clinic_name', 'Clinic Care') }}</h1>
                <p>{{ \App\Support\Registry::setting('clinic_address') }} ·
                    {{ \App\Support\Registry::setting('clinic_phone') }}</p>
                <p>{{ now()->format('Y-m-d H:i') }}</p>
            </div>
            <div class="heading">
                <div>
                    <div class="eyebrow">CLINIC CARE /
                        {{ \App\Support\Registry::modules()[$module ?? 'dashboard']['title'] ?? ($module ?? 'dashboard') }}
                    </div>
                    <h1>{{ $title ?? 'ড্যাশবোর্ড' }}</h1>
                    <p>রোগী, সেবা ও হিসাব এক জায়গায়</p>
                </div>
                @yield('heading-action')
            </div>

            @if (session('success'))
                <div class="success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

            @endif
            @yield('content')
            <div class="footer">
                <span>© {{ date('Y') }} Clinic Care · Soft Host IT</span>
                <span>{{ now()->format('d M Y H:i') }}</span>
            </div>
        </div>
    </main>

    {{-- jQuery + jQuery UI + Timepicker addon (CDN) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/select2-ajax.js') }}?v=2"></script>
    <script src="{{ asset('assets/app.js') }}"></script>

    <script>
        /*
         * Reusable pickers — যেকোনো পেজে শুধু class দিলেই কাজ করবে:
         *   <input class="js-date">      → শুধু তারিখ     (15-10-2026)
         *   <input class="js-datetime">  → তারিখ + সময়  (15-10-2026 14:30)
         * Modal / ajax দিয়ে HTML লোড হলে: window.initPickers(modalElement)
         */
        window.initPickers = function(root) {
            var $root = $(root || document);

            $root.find('.js-date').not('.hasDatepicker')
                .attr({
                    autocomplete: 'off',
                    placeholder: 'Select date'
                })
                .datepicker({
                    dateFormat: 'dd-mm-yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '-100:+5'
                });

            $root.find('.js-datetime').not('.hasDatepicker')
                .attr({
                    autocomplete: 'off',
                    placeholder: 'Select date & time'
                })
                .datetimepicker({
                    dateFormat: 'dd-mm-yy',
                    timeFormat: 'hh:mm TT',
                    controlType: 'select',
                    oneLine: true,
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '-100:+5'
                });
        };

        $(function() {
            window.initPickers(document);
        });
    </script>

    @stack('scripts')
</body>

</html>
