<footer class="footer">

    <div class="footer-inner d-flex justify-content-between align-items-center flex-wrap gap-2">

        {{-- LEFT --}}
        <div class="footer-left">
            <strong>&copy; {{ date('Y') }} Penmad DKI Jakarta.</strong>
            <span class="text-muted">
                Semua hak cipta dilindungi.
            </span>
        </div>

        {{-- RIGHT --}}
        <div class="footer-right d-flex align-items-center gap-2">

            <span class="version-badge">
                v1.0.0
            </span>

            <img src="{{ asset('assets/images/kemenag/penmad.png') }}" alt="logo" class="footer-logo">

        </div>

    </div>

</footer>
