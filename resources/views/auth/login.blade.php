<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login | UMP GTK</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    html,body{
      height:100%;
      margin:0;
      padding:0;
      width:100%;
      overflow-x:hidden;
      font-family: "Poppins", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    :root{
      --navy: #0f3552;
      --navy-dark: #0b2b44;
      --green: #3fb05a;
      --green-dark: #2f8f46;
      --muted: #f6f8fb;
    }

    .auth-full{
      min-height:100vh;
      width:100vw;
      display:flex;
      flex-direction:row;
    }

    .hero-left{
      flex:1 1 50%;
      min-width:0;
      background: linear-gradient(180deg, var(--navy), var(--navy-dark));
      color: #eaf7ec;
      display:flex;
      align-items:center;
      justify-content:center;
      text-align:center;
      gap:18px;
      padding:48px;
    }
    .hero-inner{ max-width:480px; width:100%; margin:auto; display:flex; flex-direction:column; align-items:center; gap:18px; }
    .hero-emblem{ width:180px; height:auto; object-fit:contain; filter: drop-shadow(0 8px 22px rgba(0,0,0,0.25)); }
    .hero-title{ margin:0; font-size:26px; font-weight:700; color:#39b04f; line-height:1.05; }
    .hero-sub{ margin:0; font-size:13px; color: rgba(255,255,255,0.85); }

    .form-right{
      flex:1 1 50%;
      min-width:0;
      display:flex;
      align-items:center;
      justify-content:center;
      background:#fff;
      padding:48px 56px;
    }
    .form-wrap{ width:100%; max-width:420px; }

    .brand-logo{ height:46px; object-fit:contain; display:inline-block; }

    .form-title{ font-size:24px; font-weight:700; margin:8px 0 16px 0; color:#162029; }

    .form-control{ border-radius:50px; padding:12px 18px; border:1px solid #e6edf2; box-shadow:none;  border: 1px solid var(--green) !important;}
    .form-control:focus{ box-shadow: 0 8px 20px rgba(47,143,70,0.08); border-color: var(--green-dark); }
    .btn-pill{ border-radius:50px; padding:10px 16px; font-weight:600; }

    .muted-small{ font-size:13px; color:#6b7280; }
    .g-0 { gap: 0 !important; }

    @media (max-width: 992px) {
      .auth-full { flex-direction:column; min-height:100vh; }
      .hero-left { display: none !important; }
      .form-right { flex: 1 1 100%; width:100%; padding:28px; }
      .brand-inline { display:block; margin-bottom:10px; }
    }

    .brand-inline { display:none; }

    @media (max-width:420px){
      .form-right { padding:18px; }
      .hero-title { font-size:20px; }
    }
  </style>
</head>
<body>
@if ($errors->any())
  <div class="alert alert-danger">
    {{ $errors->first() }}
  </div>
@endif
  <main class="auth-full" role="main" aria-labelledby="login-title">
    <section class="hero-left" aria-hidden="true">
      <div class="hero-inner" role="presentation">
        <img src="{{ asset('assets/images/kemenag/kemenag.png') }}" alt="Emblem" class="hero-emblem" />
        <div>
          <h1 class="hero-title">Upah Minimum Provinsi</h1>
          <p class="hero-sub">Guru & Tenaga Kependidikan</p>
          <p class="hero-sub">DKI Jakarta</p>
        </div>
      </div>
    </section>

    <section class="form-right">
      <div class="form-wrap" aria-labelledby="login-title">
        <img src="{{ asset('assets/images/kemenag/penmad.png') }}" alt="Logo" class="brand-logo brand-inline" />
        <div class="mb-1 d-none d-lg-flex justify-content-end">
            <img src="{{ asset('assets/images/kemenag/penmad.png') }}" alt="Logo" class="brand-logo" />
        </div>

        <h2 id="login-title" class="form-title">Login</h2>

        <form action="{{ route('login.submit') }}" method="POST" novalidate>
          @csrf
          <div class="mb-3">
            <label for="username" class="form-label small fw-semibold">Username</label>
            <input id="username" name="username" type="text" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
            @error('username')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="password" class="form-label small fw-semibold">Password</label>
            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukan Password" autocomplete="current-password" required>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="d-grid gap-2 mb-2">
            <button type="submit" class="btn btn-success btn-pill">Masuk</button>
          </div>
          
        </form>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
