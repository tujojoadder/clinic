<header>
    <button class="mobile" id="menuToggle" aria-label="মেনু খুলুন">☰</button>
    <h1>{{ \App\Support\Registry::setting('clinic_name', 'Clinic Care') }}</h1>
    <form action="{{ route('entities.index', 'patients') }}" method="get">
        <input class="search" name="q" placeholder="রোগীর নাম বা মোবাইল খুঁজুন">
    </form>
    <div class="user">
        <span class="avatar">FH</span>
        <div>
            <b>{{ auth()->user()->name }}</b>
            <small>{{ auth()->user()->role }}</small>
        </div>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="btn">লগআউট</button>
        </form>
    </div>
</header>
