@extends('layouts.base')

@section('title', 'Ubah Password')

@section('content')

    <div class="container-fluid">

        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card card-outline card-primary shadow">

                    <div class="card-header text-center">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-key mr-1"></i> Ubah Password
                        </h3>
                    </div>

                    <form action="{{ route('auth.ubah_password.update') }}" method="POST">
                        @csrf

                        <div class="card-body">

                            {{-- PASSWORD LAMA --}}
                            <div class="form-group">
                                <label>Password Lama</label>

                                <div class="input-group">
                                    <input type="password" name="password_lama" id="password_lama"
                                        class="form-control @error('password_lama') is-invalid @enderror"
                                        placeholder="Masukkan password lama" required>

                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary toggle-password"
                                            data-target="password_lama">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                @error('password_lama')
                                    <span class="invalid-feedback d-block">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>


                            {{-- PASSWORD BARU --}}
                            <div class="form-group">
                                <label>Password Baru</label>

                                <div class="input-group">
                                    <input type="password" name="password_baru" id="password_baru"
                                        class="form-control @error('password_baru') is-invalid @enderror"
                                        placeholder="Masukkan password baru" required>

                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary toggle-password"
                                            data-target="password_baru">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Password Strength --}}
                                <div class="progress mt-2" style="height:6px;">
                                    <div id="password-strength" class="progress-bar bg-danger" style="width:0%"></div>
                                </div>

                                <small class="text-muted">
                                    Password minimal <b>8 karakter</b>, mengandung <b>huruf besar</b> dan <b>angka</b>.
                                </small>

                                @error('password_baru')
                                    <span class="invalid-feedback d-block">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>


                            {{-- KONFIRMASI PASSWORD --}}
                            <div class="form-group">
                                <label>Konfirmasi Password Baru</label>

                                <div class="input-group">
                                    <input type="password" name="password_baru_confirmation" id="password_confirm"
                                        class="form-control" placeholder="Ulangi password baru" required>

                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary toggle-password"
                                            data-target="password_confirm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <small id="password-match" class="text-danger d-none">
                                    Password tidak sama
                                </small>

                            </div>

                        </div>

                        <div class="card-footer text-right">

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Update Password
                            </button>

                        </div>

                    </form>

                </div>

            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        /* ================================
       SWEET ALERT SESSION
    ================================ */

        @if (session('swal_success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('swal_success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if (session('swal_error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('swal_error') }}"
            });
        @endif


        /* ================================
           SHOW / HIDE PASSWORD
        ================================ */
        document.querySelectorAll('.toggle-password').forEach(btn => {

            btn.addEventListener('click', function() {

                const target = document.getElementById(this.dataset.target);

                if (target.type === "password") {
                    target.type = "text";
                    this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    target.type = "password";
                    this.innerHTML = '<i class="fas fa-eye"></i>';
                }

            });

        });


        /* ================================
           PASSWORD STRENGTH
        ================================ */

        const passwordInput = document.getElementById("password_baru");
        const strengthBar = document.getElementById("password-strength");

        passwordInput.addEventListener("keyup", function() {

            let value = passwordInput.value;
            let strength = 0;

            if (value.length >= 8) strength += 30;
            if (/[A-Z]/.test(value)) strength += 30;
            if (/[0-9]/.test(value)) strength += 20;
            if (/[^A-Za-z0-9]/.test(value)) strength += 20;

            strengthBar.style.width = strength + "%";

            if (strength < 40) {
                strengthBar.className = "progress-bar bg-danger";
            } else if (strength < 70) {
                strengthBar.className = "progress-bar bg-warning";
            } else {
                strengthBar.className = "progress-bar bg-success";
            }

        });


        /* ================================
           PASSWORD MATCH CHECK
        ================================ */

        const confirmInput = document.getElementById("password_confirm");
        const matchText = document.getElementById("password-match");

        confirmInput.addEventListener("keyup", function() {

            if (confirmInput.value !== passwordInput.value) {
                matchText.classList.remove("d-none");
            } else {
                matchText.classList.add("d-none");
            }

        });
    </script>
@endpush
