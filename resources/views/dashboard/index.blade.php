@extends('layouts.base')

@section('title', 'Dashboard')
<style type="text/css">
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
        /* atau sesuaikan */
        text-align: center;
    }
</style>

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Dashboard</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            {{-- Welcome --}}
            <div class="alert alert-info rounded-3 shadow-sm d-flex align-items-center">
                <i class="fas fa-user-circle fa-2x me-3 mr-2"></i>
                <div>
                    <h3 class="mb-1 fw-bold ">Selamat Datang, <strong>{{ auth()->user()->name }}</strong></h3>
                    <small>
                        Unit Kerja : <strong>{{ auth()->user()->madrasah->nama_madrasah ?? 'KANWIL DKI JAKARTA' }}</strong>
                    </small>
                </div>
            </div>

            {{-- Dashboard Cards --}}
            <div class="row mt-4">

                {{-- Kiri: Total Pegawai --}}
                <div class="col-lg-4 col-md-12 mb-4 pegawai-kiri">
                    <div class="card shadow-lg border-0 h-100 text-white"
                        style="background: linear-gradient(135deg, #4e73df, #224abe);">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                            <p class="">Total Pegawai</p>
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p class="card-text">{{ $totalPegawai }} Orang</p>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Jabatan --}}
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

                                        {{-- ICON --}}
                                        <div class="icon-box me-4">
                                            <i
                                                class="fas {{ $ikon[$jabatan] ?? 'fa-user' }} fa-2x text-{{ $warna[$jabatan] ?? 'dark' }}"></i>
                                        </div>

                                        {{-- TEXT --}}
                                        <div class="flex-grow-2 ml-2">
                                            <div class="fw-semibold text-uppercase mb-1"
                                                style="letter-spacing: 1px; color: #000; font-size: 16px;">
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
        </div>
    </section>
@endsection
