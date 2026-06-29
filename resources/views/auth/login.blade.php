<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Login | Honorarium GTK Non-PNS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="icon" href="{{ asset('assets/images/kemenag/kemenag.png') }}" />

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --blue-primary: #1d4ed8;
            --blue-hover: #2563eb;
            --blue-soft: #eff6ff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border: #e5e7eb;
            --bg: #f8fafc;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(29, 78, 216, 0.15);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            font-family: "Poppins", sans-serif;
            overflow: hidden;
            background: radial-gradient(circle at top, #e0e7ff, #f8fafc);
            /* FIX SCROLL & SWAL SHIFT */
        }

        /* CENTER WRAPPER */
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* CARD */
        .login-box {
            width: 100%;
            max-width: 920px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
            overflow: hidden;
        }

        /* LEFT */
        .login-left {
            background: linear-gradient(160deg, #1d4ed8, #1e40af);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
        }

        .login-left img {
            width: 130px;
            margin-bottom: 20px;
        }

        .login-left h1 {
            font-size: 20px;
            font-weight: 700;
        }

        .login-left p {
            font-size: 13px;
            opacity: 0.9;
        }

        /* RIGHT */
        .login-right {
            padding: 45px;
            display: flex;
            align-items: center;
        }

        .form-box {
            width: 100%;
        }

        .logo {
            width: 65px;
            display: block;
            margin: 0 auto 20px;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 25px;
        }

        /* =========================
        FORM HOVER BLUE SYSTEM
        ========================= */

        /* INPUT NORMAL */
        .form-control {
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 12px;
            font-size: 14px;
            transition: all 0.25s ease;
            background: #f8fafc;
        }

        /* INPUT FOCUS / HOVER */
        .form-control:hover {
            border-color: var(--blue-hover);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .form-control:focus {
            border-color: var(--blue-primary);
            box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.18);
            transform: translateY(-1px);
        }

        /* =========================
        BUTTON HOVER (BLUE LIFT EFFECT)
        ========================= */

        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            background: #1d4ed8;
            color: white;
            font-weight: 600;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.3px;
        }

        /* hover effect */
        .btn-login:hover {
            background: var(--blue-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.25);
        }

        /* click effect */
        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.15);
        }

        .error-text {
            font-size: 12px;
            color: #dc2626;
            margin-top: 5px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .login-box {
                grid-template-columns: 1fr;
            }

            .login-left {
                display: none;
            }

            .login-right {
                padding: 30px;
            }
        }

        /* 🔥 FIX SWEETALERT SHIFT ISSUE */
        body.swal2-shown {
            padding-right: 0 !important;
        }
    </style>
</head>

<body>

    <div class="login-wrapper">

        <div class="login-box">

            <!-- LEFT -->
            <div class="login-left">
                <div>
                    <img src="{{ asset('assets/images/kemenag/kemenag.png') }}" alt="">

                    <h1>Bidang Pendidikan Madrasah</h1>

                    <p style="margin-top:8px;">
                        Sistem Manajemen<br>
                        Honorarium GTK Non-PNS
                    </p>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="login-right">
                <div class="form-box">

                    <div style="text-align:center; margin-bottom:18px;">
                        <img src="{{ asset('assets/images/kemenag/penmad.png') }}" style="width:60px; opacity:0.9;"
                            alt="">

                        <div style="margin-top:10px; font-size:18px; font-weight:700; color:#0f172a;">
                            Honorarium GTK Non-PNS
                        </div>

                        <div style="font-size:13px; color:#64748b; margin-top:5px;">
                            Silakan masuk menggunakan akun yang telah terdaftar
                        </div>
                    </div>

                    <form action="{{ route('login.submit') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <input type="text" name="username"
                                class="form-control @error('username') is-invalid @enderror" placeholder="Username">
                        </div>

                        <div class="mb-3">
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                        </div>

                        <button class="btn-login">
                            Masuk Sistem
                        </button>

                        <div style="text-align:center; font-size:12px; color:#94a3b8; margin-top:12px;">
                            © Pendidikan Madrasah <br /> Kanwil Kementerian Agama DKI Jakarta
                        </div>
                    </form>

                </div>
            </div>

        </div>

    </div>

    <!-- SWEETALERT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            @if (session('swal_success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('swal_success') }}"
                });
            @endif

            @if (session('swal_error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "{{ session('swal_error') }}"
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: "{{ $errors->first() }}"
                });
            @endif

        });
    </script>

</body>

</html>
