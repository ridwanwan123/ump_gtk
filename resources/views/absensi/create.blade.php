@extends('layouts.base')

@section('title', 'Input Absensi Pegawai')

@push('styles')
    <style>
        /* ------------------------------ Tabel ------------------------------- */
        .table-responsive {
            max-height: 520px;
            overflow-x: auto;
            overflow-y: auto;
            font-family: 'Arial', sans-serif;
            font-size: 0.85rem;
        }

        table.table-sm th,
        table.table-sm td {
            padding: 0.5rem 0.6rem !important;
            font-size: 0.85rem !important;
            vertical-align: middle !important;
            white-space: nowrap;
        }

        /* Sticky header */
        .table thead th {
            position: sticky;
            top: 0;
            background-color: #f4f6f9;
            z-index: 2;
        }

        /* Nama Pegawai */
        td.nama-pegawai {
            font-weight: 500;
            font-size: 0.8rem;
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Header sub kolom (S I TK DL C) */
        th.sub-header {
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Warna Bulan */
        .bulan-pertama {
            background-color: #ffe5e5 !important;
        }

        .bulan-kedua {
            background-color: #e5ffe5 !important;
        }

        .bulan-ketiga {
            background-color: #e5e5ff !important;
        }

        /* Striping baris ganjil */
        .table tbody tr:nth-child(odd) td {
            background-color: rgba(0, 0, 0, 0.025);
        }

        tbody tr:nth-child(odd) .bulan-pertama {
            background-color: #f9cccc !important;
        }

        tbody tr:nth-child(odd) .bulan-kedua {
            background-color: #ccf9cc !important;
        }

        tbody tr:nth-child(odd) .bulan-ketiga {
            background-color: #ccccf9 !important;
        }

        /* Hover highlight */
        .table tbody tr:hover td {
            background-color: rgba(23, 162, 184, 0.1);
        }

        /* Input absensi */
        input.form-control-sm {
            font-size: 0.75rem;
            padding: 0.2rem;
            height: calc(1.4em + 0.4rem + 2px);
            text-align: center;
        }

        /* Hilangkan tombol increment/decrement di input number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Bold untuk angka selain 0 */
        input.non-zero {
            font-weight: 700;
            color: #000 !important;
            /* paksa hitam */
        }

        /* Muted untuk angka 0 */
        input.zero {
            font-weight: normal;
            color: #6c757d !important;
            /* paksa abu-abu */
        }

        /* Badge keterangan */
        .absensi-keterangan {
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
            padding: 0.25rem 0.5rem;
            background-color: #e2f0fb;
            border-left: 3px solid #17a2b8;
            line-height: 1.3;
            text-align: center;
        }

        .absensi-keterangan span.badge {
            margin-right: 0.5rem;
            font-size: 0.75rem;
        }

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
                padding-bottom: 1rem;
            }

            table.table-sm th,
            table.table-sm td {
                padding: 0.75rem 1rem !important;
                font-size: 0.9rem !important;
                white-space: nowrap;
            }

            input.form-control-sm {
                padding: 0.3rem 0.5rem;
                font-size: 0.85rem;
                min-width: 40px;
            }
        }

    </style>
@endpush

@section('content')
    <div class="container-fluid p-2">

        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="fw-bold">Input Absensi</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('absensi.index') }}"
                    class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        @php
            $tw = request('tw', ceil(now()->month / 3));
            $tahun = request('tahun', now()->year);
        @endphp

        <div class="row mb-2">
            {{-- INFO PERIODE --}}
            <div class="col-md-6">
                <div class="d-flex align-items-center p-2 bg-white border-left border-info shadow-sm rounded h-100">
                    <div class="mr-3 text-info">
                        <i class="fas fa-calendar-alt fa-lg"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold" style="font-size: 18px;">Periode Absensi</div>
                        <div class="text-bold">
                            TW {{ $tw }} · {{ $tahun }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- INFO MODE --}}
            <div class="col-md-6">
                <div
                    class="d-flex align-items-center p-2 bg-white border-left 
                    {{ $absensiExisting->isNotEmpty() ? 'border-warning' : 'border-success' }}
                    shadow-sm rounded h-100">

                    <div class="mr-3 
                        {{ $absensiExisting->isNotEmpty() ? 'text-warning' : 'text-success' }}">
                        <i class="fas {{ $absensiExisting->isNotEmpty() ? 'fa-edit' : 'fa-plus-circle' }} fa-lg"></i>
                    </div>

                    <div>
                        <div class="font-weight-bold" style="font-size: 18px; ">
                            {{ $absensiExisting->isNotEmpty() ? 'Mode Edit Data' : 'Mode Input Baru' }}
                        </div>
                        <div class="text-bold">
                            {{ $absensiExisting->isNotEmpty()
                                ? 'Data absensi sudah ada dan akan diperbarui'
                                : 'Belum ada data absensi pada periode ini' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card card-outline card-info shadow-sm mt-4">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-calendar-check mr-1"></i> Input Absensi Pegawai
                </h3>
            </div>

            <form action="{{ route('absensi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun" value="{{ request('tahun', now()->year) }}">
                <input type="hidden" name="tw" value="{{ request('tw', ceil(now()->month / 3)) }}">

                <div class="card-body table-responsive p-1" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                    <div class="absensi-keterangan">
                        <strong>Petunjuk Pengisian:</strong><br>
                        <span class="badge badge-danger">S</span>Sakit
                        <span class="badge badge-warning">I</span>Izin
                        <span class="badge badge-secondary">TK</span>Tanpa Keterangan
                        <span class="badge badge-info">DL</span>Dinas Luar
                        <span class="badge badge-success">C</span>Cuti
                    </div>

                    <table class="table table-bordered table-hover table-sm nowrap">
                        <thead class="text-center">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Nama Pegawai</th>
                                <th rowspan="2">Jabatan Pegawai</th>
                                @foreach ($bulan as $index => $b)
                                    @php
                                        $kelasBulan = ['bulan-pertama', 'bulan-kedua', 'bulan-ketiga'][$index];
                                    @endphp
                                    <th colspan="5" class="{{ $kelasBulan }}">{{ $b }}</th>
                                @endforeach
                            </tr>
                            <tr class="text-center">
                                @foreach ($bulan as $index => $b)
                                    @php
                                        $kelasBulan = ['bulan-pertama', 'bulan-kedua', 'bulan-ketiga'][$index];
                                    @endphp
                                    @foreach (['S', 'I', 'TK', 'DL', 'C'] as $jenis)
                                        <th class="{{ $kelasBulan }}">{{ $jenis }}</th>
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pegawaiList as $i => $pegawai)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td class="nama-pegawai">{{ $pegawai->nama_rekening }}</td>
                                    <td class="nama-pegawai">{{ $pegawai->jabatan }}</td>

                                    @foreach ($bulan as $index => $b)
                                        @php
                                            $kelasBulan = ['bulan-pertama', 'bulan-kedua', 'bulan-ketiga'][$index];
                                            $bulanAngka = $index + ($tw - 1) * 3 + 1;
                                        @endphp

                                        @foreach (['sakit', 'izin', 'ketidakhadiran', 'dinas_luar', 'cuti'] as $key)
                                            @php
                                                $value = $absensiExisting[$pegawai->id][$bulanAngka][0]->{$key} ?? 0;
                                            @endphp

                                            <td class="{{ $kelasBulan }}">
                                                <input type="number"
                                                    name="absensi[{{ $pegawai->id }}][{{ $bulanAngka }}][{{ $key }}]"
                                                    value="{{ $value }}" placeholder="0" min="0"
                                                    step="1" class="form-control form-control-sm">
                                            </td>
                                        @endforeach
                                    @endforeach

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex align-items-center p-2">
                    <button type="submit" class="btn btn-info btn-sm ml-auto">
                        <i class="fas fa-save"></i> Simpan Semua Absensi
                    </button>
                </div>

            </form>
        </div>
    </div>
    @push('scripts')
        @if (session('swal_success'))
            <script>
                Swal.fire({
                    title: '🎉 Sukses!',
                    text: "{{ session('swal_success') }}",
                    icon: 'success',
                    iconColor: '#28a745',
                    color: '#ffffff',
                    showConfirmButton: true,
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#0D47A1',
                    timer: 1800,
                    timerProgressBar: true
                });
            </script>
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                function updateBold(input) {
                    if (input.value && input.value != '0') {
                        input.classList.add('non-zero');
                        input.classList.remove('zero');
                    } else {
                        input.classList.remove('non-zero');
                        input.classList.add('zero');
                    }
                }
                document.querySelectorAll('input[type="number"]').forEach(input => {

                    // Hapus leading zero saat ketik
                    input.addEventListener('input', (e) => {
                        if (e.target.value.length > 1 && e.target.value.startsWith('0')) {
                            e.target.value = e.target.value.replace(/^0+/, '');
                        }
                        updateBold(e.target);
                    });

                    // Focus: kosongkan 0
                    input.addEventListener('focus', (e) => {
                        if (e.target.value === '0') e.target.value = '';
                    });

                    // Blur: kembalikan 0 jika kosong
                    input.addEventListener('blur', (e) => {
                        if (e.target.value === '') e.target.value = '0';
                        updateBold(e.target);
                    });

                    // Inisialisasi saat load
                    updateBold(input);
                });
            });
        </script>
    @endpush

@endsection
