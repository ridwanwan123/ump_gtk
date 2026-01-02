<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Penmad</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/kemenag/kemenag.png') }}" />

    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    @stack('styles')

    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
        }

        /* ---------- Sidebar Gradient & Hover ---------- */
        .main-sidebar {
            background: linear-gradient(180deg, #1e3c72, #2a5298);
        }

        .nav-sidebar>.nav-item>.nav-link {
            color: #ffffff;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .nav-sidebar>.nav-item>.nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffc107;
        }

        .nav-sidebar .nav-header {
            color: #ffc107;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 15px;
        }

        .nav-sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffc107;
        }

        /* ---------- User Panel ---------- */
        .user-panel {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease-in-out;
        }

        /* Gambar user */
        .user-panel .user-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            /* bentuk pentagon */
            clip-path: polygon(50% 0%,
                    /* atas tengah */
                    100% 30%,
                    /* kanan atas */
                    84% 100%,
                    /* kanan bawah */
                    18% 100%,
                    /* kiri bawah */
                    0% 38%
                    /* kiri atas */
                );
            border: 1px solid #ffc107;
            transition: all 0.2s ease-in-out;
        }

        /* Info teks */
        .user-panel .info {
            margin-left: 10px;
            transition: all 0.2s ease-in-out;
        }

        .user-panel .info a {
            color: #ffffff;
            font-weight: 600;
            white-space: nowrap;
        }

        .user-panel .info small {
            color: #dcdcdc;
            white-space: nowrap;
        }

        /* Brand Logo */
        .brand-link {
            background-color: rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 0.8rem 0;
        }

        .brand-link .brand-title {
            color: #ffc107;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .footer-modern {
            background: linear-gradient(90deg, #0D47A1, #1DE9B6);
            color: #ffffff;
            padding: 15px 20px;
            font-weight: 500;
            font-size: 0.9rem;
            box-shadow: 0 -3px 8px rgba(0, 0, 0, 0.15);
            transition: background 0.3s;
        }

        .footer-modern:hover {
            background: linear-gradient(90deg, #1DE9B6, #0D47A1);
        }

        .footer-modern .version-badge {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
        }

        .footer-modern .footer-logo {
            height: 28px;
            object-fit: contain;
        }

        /* Responsive footer */
        @media (max-width: 576px) {
            .footer-modern {
                flex-direction: column;
                text-align: center;
            }

            .footer-modern .d-flex {
                justify-content: center;
                margin-top: 8px;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @include('layouts.navbar')

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ asset('assets/index3.html') }}" class="brand-link">
                <span class="brand-text brand-title">Sistem UMP GTK</span>
            </a>
            @include('layouts.sidebar')
        </aside>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>

        <footer class="main-footer footer-modern">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <div>
                    <strong>&copy; 2025 Penmad Jakarta.</strong>
                    <span class="d-none d-md-inline">Semua hak cipta dilindungi.</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="version-badge">v1.1.0</span>
                    <img src="{{ asset('assets/images/kemenag/penmad.png') }}" alt="Logo" class="footer-logo ms-3 ml-2">
                </div>
            </div>
        </footer>


        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>

    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
