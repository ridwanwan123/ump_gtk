<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-flex align-items-center" style="max-width: 250px;">
            <!-- Teks bisa wrap dan responsive -->
            <a class="nav-link p-0" href="#" role="button"
                style="white-space: normal; font-weight: 600; font-size: 1rem;">
                UMP Guru &amp; Tenaga Kependidikan
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</nav>

<style>
    /* Agar teks di navbar kiri wrap dengan baik di layar kecil */
    @media (max-width: 576px) {
        .main-header .navbar-nav>li.nav-item.d-flex {
            max-width: 150px !important;
            /* Lebar maksimal di mobile */
        }

        .main-header .navbar-nav>li.nav-item.d-flex a.nav-link {
            font-size: 0.85rem !important;
            /* Font lebih kecil di mobile */
            white-space: normal !important;
            /* Bisa wrap */
        }
    }
</style>
