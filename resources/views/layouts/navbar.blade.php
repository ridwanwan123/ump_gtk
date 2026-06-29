<nav class="topbar d-flex justify-content-between align-items-center">

    {{-- LEFT --}}
    <div class="left d-flex align-items-center gap-2">

        {{-- Toggle Sidebar (AdminLTE pushmenu tetap dipakai) --}}
        <a href="#" class="toggle-btn" data-widget="pushmenu" role="button">
            <i class="bi bi-list"></i>
        </a>

        {{-- Logo Kemenag --}}
        <img src="{{ asset('assets/images/kemenag/kemenag.png') }}" alt="logo" class="topbar-logo">

        {{-- App Title --}}
        <div class="app-title">
            <span class="app-name">
                Honorarium GTK Non-PNS
            </span>

            <span class="app-region">
                Penmad DKI Jakarta
            </span>
        </div>

    </div>

    {{-- RIGHT --}}
    <div class="right d-flex align-items-center gap-3">

        {{-- Notification --}}
        <div class="topbar-icon">
            <i class="bi bi-bell"></i>
        </div>

        {{-- Fullscreen (opsional tetap AdminLTE) --}}
        <div class="topbar-icon" data-widget="fullscreen">
            <i class="bi bi-arrows-fullscreen"></i>
        </div>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>

        </form>

    </div>

</nav>
