<!doctype html>
<html lang="bn">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">

    <title>Clinic Care · লগইন : Clinic Management Software</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/login-desktop.css') }}">
    <script src="{{ asset('assets/login-desktop.js') }}" defer></script>
</head>

<body class="cc-login">
    @php $isDemo = request()->getHost() === 'clinic.amarhisab.com'; @endphp
    <div class="cc-shell">
        <section class="cc-story" aria-labelledby="story-title">
            <div class="cc-story-backdrop" aria-hidden="true"></div>
            <div class="cc-brand">
                <span class="cc-mark" aria-hidden="true">+</span>
                <div>
                    <strong>Clinic Care</strong>
                    <small>CLINIC MANAGEMENT</small>
                </div>
            </div>

            <div class="cc-story-copy">
                <span class="cc-kicker"><span aria-hidden="true"></span> যত্নের প্রতিটি ধাপে, আপনার পাশে</span>
                <h1 id="story-title">আরও সহজ পরিচালনা।<br>আরও ভালো সেবা।</h1>
                <p>রোগীর প্রথম ভিজিট থেকে ভর্তি ও বিল—<br>আপনার ক্লিনিকের সব কাজ এক জায়গায়।</p>
                <div class="cc-pills" aria-label="সফটওয়্যারের সুবিধা">
                    <span>রোগী ও সিরিয়াল</span>
                    <span>ভর্তি ও টেস্ট</span>
                    <span>বিল ও রিপোর্ট</span>
                </div>
            </div>

            <div class="cc-story-footer">
                <span class="cc-line" aria-hidden="true"></span>
                <span>আপনার টিম। আপনার ক্লিনিক। এক ওয়ার্কস্পেস।</span>
            </div>
        </section>

        <main class="cc-main">
            <div class="cc-form-panel">
                <div class="cc-brand cc-mobile-brand">
                    <span class="cc-mark" aria-hidden="true">+</span>
                    <div>
                        <strong>Clinic Care</strong>
                        <small>CLINIC MANAGEMENT</small>
                    </div>
                </div>

                <span class="cc-eyebrow">CLINIC CARE WORKSPACE</span>
                <h2>স্বাগতম, আবারও</h2>
                <p class="cc-description">লগইন করুন। আজকের কাজ শুরু হোক এখান থেকেই।</p>

                @if ($errors->any())
                    <div class="cc-error" role="alert" id="login-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="post" action="{{ route('login.store') }}" class="cc-form">
                    @csrf

                    <div class="cc-field">
                        <label for="email">ইমেইল অ্যাড্রেস</label>
                        <input type="email" id="email" name="email"
                            value="{{ old('email', $isDemo ? 'zcaptcha@gmail.com' : '') }}"
                            placeholder="name@example.com" maxlength="190" autocomplete="username" spellcheck="false"
                            autocapitalize="none" required>
                    </div>

                    <div class="cc-field">
                        <label for="password">পাসওয়ার্ড</label>
                        <div class="cc-password-wrap">
                            <input type="password" id="password" name="password"
                                value="{{ $isDemo ? '123456780000' : '' }}" placeholder="আপনার পাসওয়ার্ড লিখুন"
                                maxlength="128" autocomplete="current-password" required>
                            <button type="button" class="cc-password-toggle" id="toggle-password"
                                aria-controls="password" aria-pressed="false" aria-label="পাসওয়ার্ড দেখান" hidden>
                                দেখুন
                            </button>
                        </div>
                        <span id="caps-warning" class="cc-caps-warning" role="status" hidden>
                            Caps Lock চালু আছে।
                        </span>
                    </div>

                    <button type="submit" class="cc-submit">
                        <span>লগইন করুন</span>
                        <span aria-hidden="true">→</span>
                    </button>
                </form>

                <div class="cc-help">
                    <span class="cc-help-icon" aria-hidden="true">?</span>
                    <p>অ্যাকাউন্টে প্রবেশ করতে সমস্যা হলে<br>আপনার ক্লিনিকের অ্যাডমিনের সঙ্গে যোগাযোগ করুন।</p>
                </div>
            </div>

            <footer class="cc-footer">
                <span>© {{ date('Y') }} Clinic Care</span>
                <span>ক্লিনিক পরিচালনা, আরও সহজ।</span>
            </footer>
        </main>
    </div>
</body>

</html>
