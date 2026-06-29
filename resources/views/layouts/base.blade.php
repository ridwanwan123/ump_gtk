<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Penmad') | Kanwil Kemenag DKI Jakarta</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Font Awesome (sementara untuk icon lama) --}}
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">

    {{-- AdminLTE --}}
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">

    {{-- Plugin --}}
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    {{-- Base Design System --}}
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}">

    <link rel="icon" href="{{ asset('assets/images/kemenag/kemenag.png') }}">

    @stack('styles')
    
</head>

<body>

    <div class="app-wrapper">
        <div class="bg-ornament"></div>

        {{-- SIDEBAR --}}
        @include('layouts.sidebar')

        {{-- MAIN AREA --}}
        <div class="main">
            {{-- TOPBAR --}}
            @include('layouts.navbar')

            {{-- CONTENT (ONLY ONE SCROLL SYSTEM) --}}
            <main class="main-content">

                @hasSection('breadcrumb')
                    <div class="page-header">
                        @yield('breadcrumb')
                    </div>
                @endif

                <div class="content-wrapper-custom">
                    @yield('content')
                </div>

            </main>

            {{-- FOOTER (NO SCROLL) --}}
            @include('layouts.footer')

        </div>
    </div>

    {{-- JQuery --}}
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>

    {{-- Bootstrap --}}
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    {{-- AdminLTE --}}
    <script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>

    {{-- Plugins --}}
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- Base JS --}}
    <script src="{{ asset('assets/js/base.js') }}"></script>

    @stack('scripts')

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const sidebar = document.querySelector(".sidebar");
            const toggleBtn = document.querySelector(".toggle-btn");

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener("click", function() {
                    sidebar.classList.toggle("collapsed");
                });
            }

        });
    </script>
</body>

</html>
