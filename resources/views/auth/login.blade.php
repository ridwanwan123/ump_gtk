<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Login | UMP GTK</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/kemenag/kemenag.png') }}" />

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css">

    <style>
        /* Reset & base */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            width: 100%;
            overflow-x: hidden;
            font-family: "Poppins", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background: linear-gradient(135deg, #0D47A1 0%, #1DE9B6 100%);
            color: #162029;
        }

        /* Container utama: flex dengan split kiri kanan */
        .auth-full {
            min-height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: row;
            box-shadow: inset 0 0 150px rgba(0, 0, 0, 0.15);
        }

        /* Bagian kiri dengan background gradien dan teks */
        .hero-left {
            flex: 1 1 50%;
            min-width: 0;
            background: linear-gradient(180deg, #0D47A1, #1DE9B6);
            color: #eaf7ec;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 48px;
            position: relative;
            overflow: hidden;
        }

        /* Animasi lembut gambar emblem */
        .hero-emblem {
            width: 180px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 8px 25px rgba(0, 0, 0, 0.3));
            animation: floatUpDown 4s ease-in-out infinite;
            margin-bottom: 28px;
        }

        @keyframes floatUpDown {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        /* Judul utama */
        .hero-title {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            line-height: 1.1;
            letter-spacing: 1.3px;
        }

        /* Subjudul */
        .hero-sub {
            margin: 6px 0 0;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.85);
            letter-spacing: 0.8px;
            font-weight: 600;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }

        /* Bagian kanan untuk form */
        .form-right {
            flex: 1 1 50%;
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            padding: 48px 56px;
            box-shadow: -8px 0 30px -5px rgba(13, 71, 161, 0.3);
            border-radius: 0 12px 12px 0;
            position: relative;
        }

        /* Wrapper form */
        .form-wrap {
            width: 100%;
            max-width: 420px;
        }

        /* Logo Penmad di atas form */
        .brand-logo {
            height: 56px;
            object-fit: contain;
            display: block;
            margin: 0 auto 24px;
            filter: drop-shadow(0 2px 6px rgba(13, 71, 161, 0.25));
            transition: transform 0.3s ease;
        }

        .brand-logo:hover {
            transform: scale(1.1);
        }

        /* Judul form */
        .form-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 24px;
            color: #0D47A1;
            text-align: center;
        }

        /* Style input */
        .form-control {
            border-radius: 50px;
            padding: 14px 20px;
            border: 2px solid #3fb05a;
            box-shadow: none;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-control:focus {
            box-shadow: 0 8px 20px rgba(63, 176, 90, 0.3);
            border-color: #2f8f46;
            outline: none;
        }

        /* Tombol submit */
        .btn-pill {
            border-radius: 50px;
            padding: 12px 16px;
            font-weight: 700;
            font-size: 1.1rem;
            background: linear-gradient(90deg, #0D47A1, #1DE9B6);
            border: none;
            color: white;
            box-shadow: 0 8px 20px rgba(29, 233, 182, 0.5);
            transition: all 0.3s ease;
        }

        .btn-pill:hover,
        .btn-pill:focus {
            background: linear-gradient(90deg, #1DE9B6, #0D47A1);
            box-shadow: 0 12px 30px rgba(13, 71, 161, 0.6);
            outline: none;
        }

        /* Feedback error */
        .invalid-feedback {
            font-size: 0.9rem;
            font-weight: 600;
            color: #d6336c;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .auth-full {
                flex-direction: column;
                min-height: 100vh;
            }

            .hero-left {
                display: none !important;
            }

            .form-right {
                flex: 1 1 100%;
                width: 100%;
                padding: 36px 28px;
                border-radius: 0;
                box-shadow: none;
            }
        }

        @media (max-width: 420px) {
            .form-right {
                padding: 28px 18px;
            }

            .form-title {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>

    <main class="auth-full" role="main" aria-labelledby="login-title">
        <section class="hero-left" aria-hidden="true">
            <div class="hero-inner" role="presentation">
                <img src="{{ asset('assets/images/kemenag/kemenag.png') }}" alt="Emblem" class="hero-emblem" />
                <h1 class="hero-title">Upah Minimum Provinsi</h1>
                <p class="hero-sub">Guru & Tenaga Kependidikan</p>
                <p class="hero-sub">DKI Jakarta</p>
            </div>
        </section>

        <section class="form-right">
            <div class="form-wrap" aria-labelledby="login-title">
                <img src="{{ asset('assets/images/kemenag/penmad.png') }}" alt="Logo" class="brand-logo" />

                <h2 id="login-title" class="form-title">Masuk ke Akun Anda</h2>

                <form action="{{ route('login.submit') }}" method="POST" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label small fw-semibold text-secondary">Username</label>
                        <input id="username" name="username" type="text"
                            class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}"
                            required autofocus>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
                        <input id="password" name="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password"
                            autocomplete="current-password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 mb-2">
                        <button type="submit" class="btn btn-pill">Masuk</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <!-- Bootstrap dan FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('swal_success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('swal_success') }}",
                    confirmButtonText: 'OK'
                });
            @endif

            @if (session('swal_error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "{{ session('swal_error') }}",
                    confirmButtonText: 'Coba Lagi'
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: "{{ $errors->first() }}",
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>
</body>

</html>
