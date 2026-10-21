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
    @stack('styles')
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.8/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets/js/select2-ajax.js') }}?v=2"></script>
    <script src="{{ asset('assets/app.js') }}"></script>
    <script src="{{ asset('assets/js/pagination.js') }}"></script>

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

    {{-- select2 normal --}}
    <script>
        /*
         * Reusable Select2 — যেকোনো <select> এ শুধু class দিলেই কাজ করবে:
         *   <select class="js-select2">
         *   <select class="js-select2" data-placeholder="বেছে নিন">
         * Modal / ajax দিয়ে HTML লোড হলে: window.initSelect2(modalElement)
         */
        window.initSelect2 = function(root) {
            $(root || document).find('select.js-select2').not('.select2-hidden-accessible').each(function() {
                var $el = $(this);
                var $modal = $el.closest('.modal, dialog, [role=dialog]');

                $el.select2({
                    width: '100%',
                    minimumResultsForSearch: 8, // ৮টার বেশি অপশন থাকলে তবেই সার্চ বক্স আসবে
                    placeholder: $el.data('placeholder') || undefined,
                    dropdownParent: $modal.length ? $modal : $(document.body)
                });
            });
        };

        $(function() {
            window.initSelect2(document);
        });
    </script>

    {{-- SweetAlert reusable helper — যেকোনো পেজের JS থেকে ডাকা যাবে --}}
    <script>
        /*
         *   confirmDelete().then(yes => { if (yes) ... })
         *   confirmDelete('এই অ্যাকাউন্ট মুছবেন?')
         *   confirmDefault().then(yes => { if (yes) ... })
         *   askConfirm({title, text, icon, confirmText, confirmColor, focusCancel}) → নিজের মতো
         *   toastOk('সংরক্ষণ হয়েছে')   → কোণায় ছোট সফল বার্তা
         *   alertError('বার্তা')
         * Swal লোড না হলে নিজে থেকে সাধারণ confirm()/alert() এ ফিরে যায়।
         * animation বন্ধ রাখা হয়েছে, তাই সঙ্গে সঙ্গে খোলে ও বন্ধ হয়।
         */
        if (window.Swal) {
            window.SwalFast = Swal.mixin({
                animation: false,
                heightAuto: false,
                scrollbarPadding: false,
                reverseButtons: true,
                cancelButtonColor: '#748587'
            });
        }

        window.askConfirm = function(opts) {
            opts = opts || {};
            if (!window.SwalFast) {
                return Promise.resolve(window.confirm(opts.text || opts.title || 'নিশ্চিত?'));
            }
            return SwalFast.fire({
                title: opts.title || 'নিশ্চিত?',
                text: opts.text || '',
                icon: opts.icon || 'question',
                showCancelButton: true,
                confirmButtonText: opts.confirmText || 'হ্যাঁ',
                cancelButtonText: opts.cancelText || 'বাতিল',
                confirmButtonColor: opts.confirmColor || '#087f73',
                focusCancel: !!opts.focusCancel
            }).then(function(r) {
                return r.isConfirmed;
            });
        };

        window.confirmDelete = function(text) {
            return window.askConfirm({
                title: 'মুছে ফেলবেন?',
                text: text || 'মুছলে এই রেকর্ড আর ফেরত পাওয়া যাবে না।',
                icon: 'warning',
                confirmText: 'হ্যাঁ, মুছুন',
                confirmColor: '#d33',
                focusCancel: true
            });
        };

        window.confirmDefault = function(text) {
            return window.askConfirm({
                title: 'ডিফল্ট করবেন?',
                text: text || 'এটিকে ডিফল্ট হিসেবে সেট করা হবে।',
                icon: 'question',
                confirmText: 'হ্যাঁ, ডিফল্ট করুন'
            });
        };

        window.toastOk = function(msg) {
            if (!window.SwalFast) return;
            SwalFast.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: msg,
                showConfirmButton: false,
                timer: 1800
            });
        };

        window.alertError = function(msg) {
            if (!window.SwalFast) {
                window.alert(msg);
                return;
            }
            SwalFast.fire({
                icon: 'error',
                title: 'সমস্যা হয়েছে',
                text: msg,
                confirmButtonText: 'ঠিক আছে',
                confirmButtonColor: '#087f73'
            });
        };
    </script>

    @stack('scripts')
</body>

</html>
