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

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 1.1.0
            </div>
            <strong>&copy; 2025 Penmad Jakarta.</strong>
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
    @stack('scripts')
</body>

</html>
