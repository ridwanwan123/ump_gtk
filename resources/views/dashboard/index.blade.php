@extends('layouts.base')

@section('title', 'Dashboard Terpadu')

@push('styles')
    <style>
        /* ---------- Kartu Jabatan ---------- */
        .card-jabatan {
            min-height: 120px;
            transition: all 0.2s ease-in-out;
        }

        .card-jabatan:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        .icon-box {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pegawai-kiri {
            font-size: 2rem;
        }

        .toptol {
            font-size: 2rem !important;
            padding: 0.5rem 1rem !important;
            width: 120px;
            text-align: center;
        }

        /* ---------- Redesigned Status Input ---------- */
        .status-wrapper {
            transition: all 0.3s ease;
        }

        .status-wrapper:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
        }

        .status-subcard {
            color: #fff;
            transition: all 0.3s ease;
            position: relative;
            border-radius: 15px;
            min-height: 180px;
        }

        .status-subcard.success {
            background: linear-gradient(135deg, #1cc88a, #13855c);
        }

        .status-subcard.danger {
            background: linear-gradient(135deg, #e74a3b, #b02a1a);
        }

        .status-subcard:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .status-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .status-title {
            font-size: 14px;
            letter-spacing: 1px;
            text-transform: uppercase;
            opacity: 0.9;
        }

        .status-number {
            font-size: 2.5rem;
            font-weight: 900;
            line-height: 1;
            margin: 10px 0;
        }

        /* ---------- Redesigned Modals ---------- */
        .modal-content {
            animation: modalFadeIn 0.4s ease-out;
            border-radius: 15px;
            overflow: hidden;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-list-item {
            transition: all 0.2s ease;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .modal-list-item:hover {
            background-color: rgba(0, 123, 255, 0.05);
            transform: translateX(3px);
        }

        /* Responsivitas */
        @media (max-width: 768px) {
            .status-number {
                font-size: 2rem;
            }

            .status-icon {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .modal-dialog {
                margin: 10px;
            }

            .modal-list-item {
                font-size: 14px;
            }
        }

        .dash-card {
            border: 1px solid #eef2f7;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            transition: all 0.2s ease-in-out;
            background: #fff;
        }

        .dash-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.10);
        }

        .dash-card-header {
            padding: 18px 20px 0 20px;
            border-bottom: none;
            background: transparent;
        }

        .dash-title {
            font-weight: 700;
            font-size: 16px;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .dash-subtitle {
            font-size: 12.5px;
            color: #64748b;
            margin-bottom: 0;
        }

        .dash-card-body {
            padding: 18px 20px 22px 20px;
        }
    </style>
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Dashboard Terpadu</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            {{-- Selamat Datang --}}
            <div class="row mb-4">

                <div class="col-12">

                    <div class="dash-card">

                        <div class="dash-card-body d-flex justify-content-between align-items-center flex-wrap gap-3">

                            {{-- LEFT --}}
                            <div>

                                <h4 class="mb-1 fw-bold text-dark">
                                    👋 Selamat Datang, {{ auth()->user()->name ?? 'User' }}
                                </h4>

                                <div class="text-muted">
                                    Berikut ringkasan data pegawai dan madrasah pada sistem
                                </div>

                            </div>

                            {{-- RIGHT --}}
                            <div class="text-end">

                                <div class="mb-1">
                                    <span class="badge bg-primary px-3 py-2 rounded-pill">
                                        📅 Tahun {{ $tahun }}
                                    </span>

                                    <span class="badge bg-success px-3 py-2 rounded-pill">
                                        🟢 TW {{ $tw }} Aktif
                                    </span>
                                </div>

                                <small class="text-muted">
                                    Periode aktif digunakan untuk seluruh rekap absensi & pembayaran
                                </small>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="row">

                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="dash-card h-100">

                        <div class="dash-card-header">
                            <div class="dash-title">Statistik Jabatan Pegawai</div>
                            <div class="dash-subtitle">Distribusi pegawai berdasarkan jabatan UMP</div>
                        </div>

                        <div class="dash-card-body">
                            <div id="chartJabatan"></div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="dash-card h-100">

                        <div class="dash-card-header">
                            <div class="dash-title">Pendidikan Terakhir</div>
                            <div class="dash-subtitle">Distribusi tingkat pendidikan pegawai</div>
                        </div>

                        <div class="dash-card-body">
                            <div id="chartPendidikan"></div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="dash-card h-100">

                        <div class="dash-card-header">
                            <div class="dash-title">Pegawai per Jenjang</div>
                            <div class="dash-subtitle">MIN, MTsN, dan MAN</div>
                        </div>

                        <div class="dash-card-body">
                            <div id="chartJenjang"></div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="dash-card h-100">

                        <div class="dash-card-header">
                            <div class="dash-title">Pegawai per Madrasah (Top 10)</div>
                            <div class="dash-subtitle">Ranking jumlah pegawai terbanyak</div>
                        </div>

                        <div class="dash-card-body">
                            <div id="chartMadrasah"></div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-4">

                    <div class="card-header bg-white border-0 pt-4">
                        <h5 class="fw-bold mb-1 text-danger">
                            Pegawai Akan Pensiun
                        </h5>

                        <small class="text-muted">
                            Data pegawai yang mendekati usia pensiun (≥ 58 tahun)
                        </small>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-hover align-middle">

                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Madrasah</th>
                                        <th>Usia</th>
                                        <th>Tanggal Pensiun</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach ($pegawaiAkanPensiun as $p)
                                        @php
                                            $lahir = \Carbon\Carbon::parse($p->tanggal_lahir);
                                            $usia = $lahir->age;
                                            $tanggalPensiun = $lahir->copy()->addYears(60);
                                            $sisa = $usia >= 60 ? 0 : 60 - $usia;
                                        @endphp

                                        <tr>

                                            <td class="fw-semibold">
                                                {{ $p->nama_simpatika }}
                                            </td>

                                            <td>
                                                {{ $p->madrasah->nama_madrasah ?? '-' }}
                                            </td>

                                            <td>{{ $p->usia }} Tahun</td>

                                            <td>{{ $p->tanggal_pensiun->translatedFormat('d F Y') }}</td>

                                            <td>
                                                @if ($p->usia >= 59)
                                                    <span class="badge bg-danger">
                                                        Sangat Dekat ({{ $p->sisa_tahun }} thn)
                                                    </span>
                                                @elseif($p->usia >= 58)
                                                    <span class="badge bg-warning text-dark">
                                                        Dekat ({{ $p->sisa_tahun }} thn)
                                                    </span>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>
            </div>

            @if (auth()->user()->hasRole('superadmin'))
                <div class="row mt-4">
                    <div class="col-lg-6 col-md-8">
                        {{-- CARD UTAMA --}}
                        <div class="card border-0 shadow-lg status-wrapper"
                            style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 15px;">
                            <div class="card-body p-4">
                                {{-- HEADER --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h4 class="fw-bold mb-1 text-dark">Status Input Absensi</h4>
                                        <small class="text-muted">Monitoring pengisian absensi madrasah secara
                                            real-time</small>
                                    </div>
                                    <div class="position-relative">
                                        <i class="fas fa-chart-pie fa-2x text-primary opacity-75"></i>
                                        {{-- Mini chart placeholder --}}
                                        <div class="position-absolute"
                                            style="top: 5px; right: 5px; width: 30px; height: 30px; background: conic-gradient(#1cc88a 0% {{ ($sudahCount / max($sudahCount + $belumCount, 1)) * 100 }}%, #e74a3b {{ ($sudahCount / max($sudahCount + $belumCount, 1)) * 100 }}% 100%); border-radius: 50%;">
                                        </div>
                                    </div>
                                </div>

                                {{-- SUB CARD --}}
                                <div class="row g-3">
                                    {{-- SUDAH --}}
                                    <div class="col-md-6">
                                        <div
                                            class="status-subcard success d-flex flex-column justify-content-center align-items-center text-center p-3">
                                            <div class="status-icon">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <h1 class="status-number">{{ $sudahCount }}</h1>
                                            <span class="status-title">Sudah Input</span>
                                            <small class="mt-2">Madrasah telah mengisi</small>
                                            <button class="btn btn-light btn-sm w-100 mt-3 fw-bold" data-toggle="modal"
                                                data-target="#madrasahSudahModal" style="border-radius: 8px;">
                                                Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- BELUM --}}
                                    <div class="col-md-6">
                                        <div
                                            class="status-subcard danger d-flex flex-column justify-content-center align-items-center text-center p-3">
                                            <div class="status-icon">
                                                <i class="fas fa-times"></i>
                                            </div>
                                            <h1 class="status-number">{{ $belumCount }}</h1>
                                            <span class="status-title">Belum Input</span>
                                            <small class="mt-2">Madrasah belum mengisi</small>
                                            @if ($belumCount > 0)
                                                <button class="btn btn-light btn-sm w-100 mt-3 fw-bold" data-toggle="modal"
                                                    data-target="#madrasahBelumModal" style="border-radius: 8px;">
                                                    Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-light btn-sm w-100 mt-3 fw-bold" disabled
                                                    style="border-radius: 8px;">Semua Sudah Input</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-8">
                        <div class="card border-0 shadow-lg status-wrapper"
                            style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 15px;">

                            <div class="card-body p-4">

                                {{-- HEADER --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h4 class="fw-bold mb-1 text-dark">Status Input Hak Pembayaran</h4>
                                        <small class="text-muted">
                                            Monitoring pengisian hak pembayaran madrasah
                                        </small>
                                    </div>

                                    <i class="fas fa-money-bill-wave fa-2x text-success opacity-75"></i>
                                </div>

                                {{-- SUB CARD --}}
                                <div class="row g-3">

                                    {{-- SUDAH --}}
                                    <div class="col-md-6">
                                        <div
                                            class="status-subcard success d-flex flex-column justify-content-center align-items-center text-center p-3">
                                            <div class="status-icon">
                                                <i class="fas fa-check"></i>
                                            </div>

                                            <h1 class="status-number">{{ $sudahHakCount }}</h1>
                                            <span class="status-title">Sudah Input</span>
                                            <small class="mt-2">Hak pembayaran lengkap</small>

                                            <button class="btn btn-light btn-sm w-100 mt-3 fw-bold" data-toggle="modal"
                                                data-target="#madrasahSudahHakModal">
                                                Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- BELUM --}}
                                    <div class="col-md-6">
                                        <div
                                            class="status-subcard danger d-flex flex-column justify-content-center align-items-center text-center p-3">

                                            <div class="status-icon">
                                                <i class="fas fa-times"></i>
                                            </div>

                                            <h1 class="status-number">{{ $belumHakCount }}</h1>
                                            <span class="status-title">Belum Input</span>
                                            <small class="mt-2">Belum mengisi hak pembayaran</small>

                                            @if ($belumHakCount > 0)
                                                <button class="btn btn-light btn-sm w-100 mt-3 fw-bold"
                                                    data-toggle="modal" data-target="#madrasahBelumHakModal">
                                                    Lihat Detail <i class="fas fa-arrow-right ms-1"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-light btn-sm w-100 mt-3 fw-bold" disabled>
                                                    Semua Sudah Input
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Modal Hak Sudah --}}
        <div class="modal fade" id="madrasahSudahHakModal" tabindex="-1" aria-labelledby="madrasahSudahHakLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-scrollable modal-lg">

                <div class="modal-content border-0 shadow-lg">

                    <div class="modal-header text-white"
                        style="background: linear-gradient(135deg, #1cc88a, #13855c); border-bottom: none;">

                        <div class="d-flex align-items-center justify-content-center w-100">
                            <div class="text-center">
                                <h5 class="modal-title mb-0" id="madrasahSudahHakLabel">
                                    Madrasah Sudah Input Hak Pembayaran
                                </h5>

                                <small class="opacity-75">
                                    Total: {{ $sudahHakCount }} Madrasah
                                </small>
                            </div>
                        </div>

                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-3" style="background: #f8f9fa;">

                        <div class="row g-3">

                            @foreach (['MIN', 'MTSN', 'MAN'] as $type)
                                <div class="col-md-4">

                                    <div class="card border-0 shadow-sm"
                                        style="border-radius: 10px; background: #ffffff;">

                                        <div class="card-header bg-success text-white text-center fw-bold"
                                            style="border-radius: 10px 10px 0 0;">

                                            <i class="fas fa-school me-2 mr-2"></i>

                                            {{ $type }}

                                            <span class="badge bg-light text-dark ms-2">
                                                {{ count($madrasahSudahHakGroup[$type] ?? []) }}
                                            </span>

                                        </div>

                                        <div class="card-body p-2">

                                            @if (!empty($madrasahSudahHakGroup[$type]))
                                                <ul class="list-group list-group-flush">

                                                    @foreach ($madrasahSudahHakGroup[$type] as $m)
                                                        <li
                                                            class="list-group-item d-flex justify-content-between align-items-center border-0 px-1 py-1 modal-list-item">

                                                            <span class="fw-semibold small">
                                                                {{ $m->nama_madrasah }}
                                                            </span>

                                                            <span class="badge bg-success small">
                                                                Sudah
                                                            </span>

                                                        </li>
                                                    @endforeach

                                                </ul>
                                            @else
                                                <div class="text-center text-muted py-3">
                                                    <i class="fas fa-info-circle fa-lg mb-2"></i>
                                                    <p class="small">Tidak ada data</p>
                                                </div>
                                            @endif

                                        </div>

                                    </div>

                                </div>
                            @endforeach

                        </div>

                    </div>

                    <div class="modal-footer border-0" style="background: #f8f9fa;">
                        <button type="button" class="btn btn-success fw-bold" data-dismiss="modal"
                            style="border-radius: 8px;">
                            <i class="fas fa-times me-1"></i>Tutup
                        </button>
                    </div>

                </div>

            </div>

        </div>

        {{-- Modal Hak Belum --}}
        <div class="modal fade" id="madrasahBelumHakModal" tabindex="-1" role="dialog"
            aria-labelledby="madrasahBelumHakLabel" aria-hidden="true">

            <div class="modal-dialog modal-dialog-scrollable modal-lg">

                <div class="modal-content border-0 shadow-lg">

                    <div class="modal-header text-white"
                        style="background: linear-gradient(135deg, #e74a3b, #b02a1a); border-bottom: none;">

                        <div class="d-flex align-items-center justify-content-center w-100">

                            <div class="text-center">
                                <h5 class="modal-title mb-0" id="madrasahBelumHakLabel">
                                    Madrasah Belum Input Hak Pembayaran
                                </h5>

                                <small class="opacity-75">
                                    Total: {{ $belumHakCount }} Madrasah
                                </small>
                            </div>

                        </div>

                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                            aria-label="Close"></button>

                    </div>

                    <div class="modal-body p-3" style="background: #f8f9fa;">

                        <div class="row g-3">

                            @foreach (['MIN', 'MTSN', 'MAN'] as $type)
                                <div class="col-md-4">

                                    <div class="card border-0 shadow-sm"
                                        style="border-radius: 10px; background: #ffffff;">

                                        <div class="card-header bg-danger text-white text-center fw-bold"
                                            style="border-radius: 10px 10px 0 0;">

                                            <i class="fas fa-school me-2 mr-2"></i>

                                            {{ $type }}

                                            <span class="badge bg-light text-dark ms-2">
                                                {{ count($madrasahBelumHakGroup[$type] ?? []) }}
                                            </span>

                                        </div>

                                        <div class="card-body p-2">

                                            @if (!empty($madrasahBelumHakGroup[$type]))
                                                <ul class="list-group list-group-flush">

                                                    @foreach ($madrasahBelumHakGroup[$type] as $m)
                                                        <li
                                                            class="list-group-item d-flex justify-content-between align-items-center border-0 px-1 py-1 modal-list-item">

                                                            <span class="fw-semibold small">
                                                                {{ $m->nama_madrasah }}
                                                            </span>

                                                            <span class="badge bg-danger small">
                                                                Belum
                                                            </span>

                                                        </li>
                                                    @endforeach

                                                </ul>
                                            @else
                                                <div class="text-center text-muted py-3">
                                                    <i class="fas fa-check-circle fa-lg mb-2 text-success"></i>
                                                    <p class="small">Semua sudah input!</p>
                                                </div>
                                            @endif

                                        </div>

                                    </div>

                                </div>
                            @endforeach

                        </div>

                    </div>

                    <div class="modal-footer border-0" style="background: #f8f9fa;">
                        <button type="button" class="btn btn-danger fw-bold" data-dismiss="modal"
                            style="border-radius: 8px;">
                            <i class="fas fa-times me-1"></i>Tutup
                        </button>
                    </div>

                </div>

            </div>

        </div>

        {{-- Modal Absensi Sudah --}}
        <div class="modal fade" id="madrasahSudahModal" tabindex="-1" aria-labelledby="madrasahSudahLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header text-white"
                        style="background: linear-gradient(135deg, #1cc88a, #13855c); border-bottom: none;">
                        <div class="d-flex align-items-center justify-content-center w-100">
                            {{-- <i class="fas fa-check-circle fa-lg me-3"></i> --}}
                            <div class="text-center">
                                <h5 class="modal-title mb-0" id="madrasahSudahLabel">Madrasah Sudah Input Absensi</h5>
                                <small class="opacity-75">Total: {{ $sudahCount }} Madrasah</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3" style="background: #f8f9fa;">
                        <div class="row g-3">
                            @foreach (['MIN', 'MTSN', 'MAN'] as $type)
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm"
                                        style="border-radius: 10px; background: #ffffff;">
                                        <div class="card-header bg-success text-white text-center fw-bold"
                                            style="border-radius: 10px 10px 0 0;">
                                            <i class="fas fa-school me-2 mr-2"></i>{{ $type }} <span
                                                class="badge bg-light text-dark ms-2">{{ count($madrasahSudah[$type] ?? []) }}</span>
                                        </div>
                                        <div class="card-body p-2">
                                            @if (count($madrasahSudah[$type] ?? []) > 0)
                                                <ul class="list-group list-group-flush">
                                                    @foreach ($madrasahSudah[$type] ?? [] as $m)
                                                        <li
                                                            class="list-group-item d-flex justify-content-between align-items-center border-0 px-1 py-1 modal-list-item">
                                                            <div class="d-flex align-items-center">
                                                                {{-- <i class="fas fa-check text-success me-2"></i> --}}
                                                                <span
                                                                    class="fw-semibold small">{{ $m->nama_madrasah }}</span>
                                                            </div>
                                                            <span class="badge bg-success small">Sudah</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="text-center text-muted py-3">
                                                    <i class="fas fa-info-circle fa-lg mb-2"></i>
                                                    <p class="small">Tidak ada data</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer border-0" style="background: #f8f9fa;">
                        <button type="button" class="btn btn-success fw-bold" data-bs-dismiss="modal"
                            style="border-radius: 8px;">
                            <i class="fas fa-times me-1"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Absensi Belum --}}
        <div class="modal fade" id="madrasahBelumModal" tabindex="-1" role="dialog"
            aria-labelledby="madrasahBelumLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header text-white"
                        style="background: linear-gradient(135deg, #e74a3b, #b02a1a); border-bottom: none;">
                        <div class="d-flex align-items-center justify-content-center w-100">
                            {{-- <i class="fas fa-exclamation-triangle fa-lg me-3"></i> --}}
                            <div class="text-center">
                                <h5 class="modal-title mb-0" id="madrasahBelumLabel">Madrasah Belum Input Absensi</h5>
                                <small class="opacity-75">Total: {{ $belumCount }} Madrasah</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3" style="background: #f8f9fa;">
                        <div class="row g-3">
                            @foreach (['MIN', 'MTSN', 'MAN'] as $type)
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm"
                                        style="border-radius: 10px; background: #ffffff;">
                                        <div class="card-header bg-danger text-white text-center fw-bold"
                                            style="border-radius: 10px 10px 0 0;">
                                            <i class="fas fa-school me-2 mr-2"></i>{{ $type }} <span
                                                class="badge bg-light text-dark ms-2">{{ count($madrasahBelum[$type] ?? []) }}</span>
                                        </div>
                                        <div class="card-body p-2">
                                            @if (count($madrasahBelum[$type] ?? []) > 0)
                                                <ul class="list-group list-group-flush">
                                                    @foreach ($madrasahBelum[$type] ?? [] as $m)
                                                        <li
                                                            class="list-group-item d-flex justify-content-between align-items-center border-0 px-1 py-1 modal-list-item">
                                                            <div class="d-flex align-items-center">
                                                                {{-- <i class="fas fa-times text-danger me-2"></i> --}}
                                                                <span
                                                                    class="fw-semibold small">{{ $m->nama_madrasah }}</span>
                                                            </div>
                                                            <span class="badge bg-danger small">Belum</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="text-center text-muted py-3">
                                                    <i class="fas fa-check-circle fa-lg mb-2 text-success"></i>
                                                    <p class="small">Semua sudah input!</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer border-0" style="background: #f8f9fa;">
                        <button type="button" class="btn btn-danger fw-bold" data-dismiss="modal"
                            style="border-radius: 8px;">
                            <i class="fas fa-times me-1"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
@push('scripts')
    <script>
        let options = {
            series: [{
                name: 'Pegawai',
                data: @json($chartData)
            }],
            chart: {
                type: 'bar',
                height: 380,
                toolbar: {
                    show: false
                }
            },

            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 6,
                    borderRadiusApplication: 'end',
                    distributed: true,
                    barHeight: '65%'
                }
            },

            colors: [
                '#2563eb',
                '#0ea5e9',
                '#06b6d4',
                '#10b981',
                '#22c55e',
                '#f59e0b',
                '#f97316',
                '#ef4444'
            ],


            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '13px',
                    fontWeight: 600
                }
            },
            legend: {
                show: false,
            },

            xaxis: {

                categories: @json($chartLabels),

                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }

            },

            yaxis: {

                labels: {
                    style: {
                        fontSize: '13px',
                        fontWeight: 500
                    }
                }

            },

            grid: {
                borderColor: '#f1f5f9'
            },

            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " Pegawai";
                    }
                }
            }

        };

        new ApexCharts(document.querySelector("#chartJabatan"), options).render();

        // Pendidikan
        const pendidikanOptions = {

            series: @json($pendidikanData),

            chart: {
                type: 'donut',
                height: 350
            },

            labels: @json($pendidikanLabels),

            colors: [
                '#2563EB',
                '#06B6D4',
                '#10B981',
                '#F59E0B',
                '#EF4444',
                '#8B5CF6',
                '#EC4899'
            ],

            legend: {
                position: 'bottom',
                horizontalAlign: 'left',
                fontSize: '13px'
            },

            dataLabels: {
                enabled: true
            },

            stroke: {
                width: 0
            },

            plotOptions: {
                pie: {
                    donut: {
                        size: '68%',
                        labels: {
                            show: true,

                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function() {
                                    return @json($totalPegawai);
                                }
                            }
                        }
                    }
                }
            },

            tooltip: {
                y: {
                    formatter: function(val) {
                        return `${val} Pegawai`;
                    }
                }
            }

        };

        new ApexCharts(
            document.querySelector("#chartPendidikan"),
            pendidikanOptions
        ).render();

        //pegawai per jenjang
        let optionsJenjang = {

            series: [{
                name: 'Pegawai',
                data: @json($jenjangData)
            }],

            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },

            colors: ['#2563EB'],

            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    borderRadius: 8,
                    distributed: true
                }
            },

            dataLabels: {
                enabled: true
            },

            xaxis: {
                categories: @json($jenjangLabels)
            },

            legend: {
                show: false
            },

            grid: {
                borderColor: '#edf2f7'
            },

            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " Pegawai";
                    }
                }
            }

        };

        new ApexCharts(
            document.querySelector("#chartJenjang"),
            optionsJenjang
        ).render();

        //pegawai per madrasah
        let optionsMadrasah = {

            series: [{
                name: 'Pegawai',
                data: @json($madrasahData)
            }],

            chart: {
                type: 'bar',
                height: 450,
                toolbar: {
                    show: false
                }
            },

            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 6,
                    barHeight: '60%',
                    distributed: true
                }
            },

            colors: [
                '#2563EB', '#3B82F6', '#60A5FA',
                '#10B981', '#34D399',
                '#F59E0B', '#FBBF24',
                '#EF4444', '#F97316', '#8B5CF6'
            ],

            dataLabels: {
                enabled: true,
                style: {
                    fontWeight: 600
                }
            },

            xaxis: {
                categories: @json($madrasahLabels),

                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },

            yaxis: {
                labels: {
                    style: {
                        fontSize: '12px',
                        fontWeight: 500
                    }
                }
            },

            grid: {
                borderColor: '#eef2f7'
            },

            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " Pegawai";
                    }
                }
            },

            legend: {
                show: false
            }

        };

        new ApexCharts(
            document.querySelector("#chartMadrasah"),
            optionsMadrasah
        ).render();
    </script>
    @if (session('swal_success'))
        <script>
            Swal.fire({
                title: '🎉 Selamat Datang!',
                text: "{{ session('swal_success') }}",
                icon: 'success',
                iconColor: '#28a745', // hijau cerah
                // background: 'linear-gradient(135deg, #1DE9B6, #0D47A1)',
                color: '#ffffff',
                showConfirmButton: true,
                confirmButtonText: 'Lanjut ke Dashboard',
                confirmButtonColor: '#ffc107',
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => {
                    const b = Swal.getHtmlContainer().querySelector('b')
                    Swal.showLoading()
                },
                willClose: () => {
                    console.log('Swal ditutup')
                }
            });
        </script>
    @endif
@endpush
