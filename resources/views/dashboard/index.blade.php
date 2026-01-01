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

        /* ---------- Hover Effect Status Madrasah ---------- */
        .hover-shadow:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        /* ---------- Rounded Progress Bar ---------- */
        .progress {
            height: 30px;
            border-radius: 20px;
            overflow: hidden;
        }

        .progress-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* ---------- Badge Modern ---------- */
        .badge-modern {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 20px;
        }

        /* ---------- Card Header ---------- */
        .card-header-modern {
            font-size: 1.1rem;
            font-weight: 600;
            color: #000;
            margin-bottom: 10px;
        }

        /* Card Hover */
        .card-jabatan {
            min-height: 120px;
            transition: all 0.2s ease-in-out;
        }

        .card-jabatan:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        /* Icon box */
        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Badge */
        .toptol {
            font-size: 1.8rem;
            padding: 0.4rem 1rem;
            margin-top: 5px;
            text-align: center;
        }

        /* Progress */
        .progress {
            background: rgba(0, 0, 0, 0.05);
        }

        .progress-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #fff;
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
                                        <div class="flex-grow-2 ml-2">
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

            {{-- Status Input Madrasah Minimalis --}}
            <div class="row mt-4">
                @php
                    $warnaStatus = [
                        'Sudah' => 'success',
                        'Belum' => 'danger',
                    ];
                    $iconStatus = [
                        'Sudah' => 'fa-check-circle',
                        'Belum' => 'fa-times-circle',
                    ];
                @endphp

                @foreach (['Sudah' => $sudahCount, 'Belum' => $belumCount] as $status => $count)
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm border-0 card-jabatan">
                            <div class="card-body d-flex align-items-center px-4 py-4">

                                {{-- Icon --}}
                                <div class="icon-box me-4">
                                    <i class="fas {{ $iconStatus[$status] }} fa-2x text-{{ $warnaStatus[$status] }}"></i>
                                </div>

                                {{-- Info --}}
                                <div class="flex-grow-2">
                                    <div class="fw-semibold text-uppercase mb-1"
                                        style="font-size:16px; letter-spacing:1px;">
                                        Madrasah {{ $status }} Input
                                    </div>

                                    <span class="badge bg-{{ $warnaStatus[$status] }} fw-bold toptol">
                                        {{ $count }}
                                    </span>

                                    @if ($status == 'Sudah')
                                        <button class="btn btn-outline-{{ $warnaStatus[$status] }} w-100 mt-2"
                                            data-toggle="modal" data-target="#madrasahSudahModal">
                                            Lihat Detail
                                        </button>
                                    @else
                                        @if ($count)
                                            <button class="btn btn-outline-{{ $warnaStatus[$status] }} w-100 mt-2"
                                                data-toggle="modal" data-target="#madrasahBelumModal">
                                                Lihat Detail
                                            </button>
                                        @endif
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Progress bar full width --}}
                <div class="col-12 mt-2">
                    <div class="progress rounded-pill" style="height:25px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentSudah }}%">
                            {{ round($percentSudah, 1) }}%
                        </div>
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $percentBelum }}%">
                            {{ round($percentBelum, 1) }}%
                        </div>
                    </div>
                </div>
            </div>



        </div>

        {{-- Modal Sudah --}}
        <div class="modal fade" id="madrasahSudahModal" tabindex="-1" aria-labelledby="madrasahSudahLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="madrasahSudahLabel">Madrasah Sudah Input Absensi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            @foreach (['MIN', 'MTSN', 'MAN'] as $type)
                                <div class="col-md-4 mb-3">
                                    <h6 class="fw-bold">{{ $type }}</h6>
                                    <ul class="list-group">
                                        @foreach ($madrasahSudah[$type] ?? [] as $m)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $m->nama_madrasah }}
                                                <span class="badge bg-primary">Sudah Input</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Belum --}}
        <div class="modal fade" id="madrasahBelumModal" tabindex="-1" role="dialog" aria-labelledby="madrasahBelumLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="madrasahBelumLabel">Madrasah Belum Input Absensi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            @foreach (['MIN', 'MTSN', 'MAN'] as $type)
                                <div class="col-md-4 mb-3">
                                    <h6 class="fw-bold">{{ $type }}</h6>
                                    <ul class="list-group">
                                        @foreach ($madrasahBelum[$type] ?? [] as $m)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $m->nama_madrasah }}
                                                <span class="badge bg-danger">Belum Input</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
