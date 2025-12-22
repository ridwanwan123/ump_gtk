@extends('layouts.base')

@section('title', 'Dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Dashboard</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        {{-- Welcome --}}
        <div class="alert alert-info">
            <h5 class="mb-1">
                <i class="fas fa-user-circle"></i>
                Selamat Datang, <strong>{{ auth()->user()->name }}</strong>
            </h5>
            <small>
                Unit Kerja :
                <strong>{{ auth()->user()->unit_kerja ?? 'KANWIL DKI JAKARTA' }}</strong>
            </small>
        </div>

        {{-- Statistik --}}
        <div class="row">

            {{-- Total Pegawai --}}
            <div class="col-lg-3 col-12">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $totalPegawai }}</h3>
                        <p>Total Pegawai</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            @php
                $warna = [
                    'Guru' => 'info',
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
                <div class="col-lg-3 col-12">
                    <div class="small-box bg-{{ $warna[$jabatan] ?? 'dark' }}">
                        <div class="inner">
                            <h3>{{ $total }}</h3>
                            <p>{{ $jabatan }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas {{ $ikon[$jabatan] ?? 'fa-user' }}"></i>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

        <div class="row mt-4" hidden>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Jumlah Pegawai per Jabatan</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="jabatanChart" style="height: 300px;"></canvas>
                    </div>
            </div>
        </div>
    </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('jabatanChart').getContext('2d');
    const jabatanChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Jumlah Pegawai',
                data: @json($chartData),
                backgroundColor: [
                    '#007bff',
                    '#dc3545',
                    '#ffc107',
                    '#28a745',
                    '#6c757d',
                    '#6f42c1',
                    '#20c997',
                    '#fd7e14'
                ],
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>
@endpush