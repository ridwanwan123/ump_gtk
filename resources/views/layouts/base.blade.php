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
</head>
<style type="text/css">
    .user-panel .user-image {
        width: 45px;
        /* default biasanya 34px */
        height: 45px;
        object-fit: contain;
    }

    .brand-title {
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
</style>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('layouts.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ asset('assets/index3.html') }}" class="brand-link">
                <span class="brand-text text-center font-weight-light brand-title">
                    <strong>Sistem UMP GTK</strong><br>
                </span>
            </a>

            <!-- Sidebar -->
            @include('layouts.sidebar')
            <!-- /.sidebar -->
        </aside>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">

                    @yield('content')

                </div>
            </section>
        </div>

        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 1.1.0
            </div>
            <strong>Copyright &copy; 2025 Penmad Jakarta.</strong>
        </footer>

        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap Bundle (Popper + Bootstrap JS) -->
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- AdminLTE -->
    <script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>

    <!-- DataTables core JS -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>

    <!-- DataTables Bootstrap4 integration -->
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- DataTables Responsive extension -->
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>


    @stack('scripts')
</body>

</html>
