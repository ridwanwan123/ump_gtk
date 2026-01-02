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
            <div class="alert alert-info rounded-3 shadow-sm d-flex align-items-center">
                <i class="fas fa-user-circle fa-2x me-3"></i>
                <div>
                    <h3 class="mb-1 fw-bold">Selamat Datang, <strong>{{ auth()->user()->name }}</strong></h3>
                    <small>Unit Kerja:
                        <strong>{{ auth()->user()->madrasah->nama_madrasah ?? 'KANWIL DKI JAKARTA' }}</strong></small>
                </div>
            </div>

            {{-- Dashboard Pegawai & Jabatan --}}
            <div class="row mt-4">
                {{-- Total Pegawai --}}
                <div class="col-lg-4 col-md-12 mb-4 pegawai-kiri">
                    <div class="card shadow-lg border-0 h-100 text-white"
                        style="background: linear-gradient(135deg, #4e73df, #224abe);">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                            <p>Total Pegawai</p>
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p class="card-text">{{ $totalPegawai }} Orang</p>
                        </div>
                    </div>
                </div>

                {{-- Statistik Jabatan --}}
                <div class="col-lg-8 col-md-12">
                    <div class="row g-3">
                        @php
                            $warna = [
                                'Guru' => 'primary',
                                'Kepala Pengelola Asrama' => 'danger',
                                'Tenaga Administrasi' => 'warning',
                                'Tenaga Keamanan' => 'success',
                                'Tenaga Kebersihan' => 'secondary',
                                'Tenaga Laboratorium' => 'purple',
                                'Tenaga Pengelola Asrama' => 'teal',
                                'Tenaga Perpustakaan' => 'orange',
                            ];

                            $ikon = [
                                'Guru' => 'fa-chalkboard-teacher',
                                'Kepala Pengelola Asrama' => 'fa-user-tie',
                                'Tenaga Administrasi' => 'fa-file-alt',
                                'Tenaga Keamanan' => 'fa-shield-alt',
                                'Tenaga Kebersihan' => 'fa-broom',
                                'Tenaga Laboratorium' => 'fa-flask',
                                'Tenaga Pengelola Asrama' => 'fa-building',
                                'Tenaga Perpustakaan' => 'fa-book',
                            ];
                        @endphp

                        @foreach ($statistikJabatan as $jabatan => $total)
                            <div class="col-md-6">
                                <div class="card shadow-sm border-0 card-jabatan">
                                    <div class="card-body d-flex align-items-center px-4 py-4">
                                        <div class="icon-box me-4">
                                            <i
                                                class="fas {{ $ikon[$jabatan] ?? 'fa-user' }} fa-2x text-{{ $warna[$jabatan] ?? 'dark' }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold text-uppercase mb-1"
                                                style="letter-spacing:1px; color:#000; font-size:16px;">
                                                {{ $jabatan }}
                                            </div>
                                            <div>
                                                <span
                                                    class="badge bg-{{ $warna[$jabatan] ?? 'secondary' }} fw-bold toptol text-center d-inline-block">
                                                    {{ $total }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
                </div>
            @endif
        </div>

        {{-- Modal Sudah --}}
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
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3" style="background: #f8f9fa;">
                        <div class="row g-3">
                            @foreach (['MIN', 'MTSN', 'MAN'] as $type)
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm" style="border-radius: 10px; background: #ffffff;">
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

        {{-- Modal Belum --}}
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

    @push('scripts')
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

@endsection
