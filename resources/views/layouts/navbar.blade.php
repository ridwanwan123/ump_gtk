<nav class="main-header navbar navbar-expand shadow-sm navbar-modern">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button" title="Toggle Sidebar">
                <i class="fas fa-bars fa-lg"></i>
            </a>
        </li>
        <li class="nav-item d-flex align-items-center ml-3">
            <a class="nav-link text-white font-weight-bold title-wrap" href="#">
                UMP Guru & Tenaga Kependidikan
            </a>
            <span class="badge badge-light ml-2 role-badge">
                {{ auth()->user()->getRoleNames()->first() }}
            </span>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item">
            <a class="nav-link text-white icon-hover" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt fa-lg"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white icon-hover" href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt fa-lg"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</nav>

<style>
    .navbar-modern {
        background: linear-gradient(90deg, #0D47A1, #1DE9B6);
        color: white;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.18);
        transition: 0.3s;
    }

    /* Hover efek ikon */
    .icon-hover i {
        transition: transform 0.2s, color 0.2s;
    }

    .icon-hover:hover i {
        transform: scale(1.2);
        color: #ffd700;
    }

    /* Badge role */
    .role-badge {
        font-size: 0.75rem;
        color: #0D47A1;
        background-color: #fff;
        font-weight: 600;
    }

    /* Judul wrap otomatis */
    .title-wrap {
        white-space: normal;
        font-size: 1.05rem;
    }

    /* Responsive navbar */
    @media (max-width: 576px) {
        .title-wrap {
            font-size: 0.9rem;
        }
    }
</style>
