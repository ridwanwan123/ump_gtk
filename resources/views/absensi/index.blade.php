@extends('layouts.base')

@section('title', 'Input Absensi Pegawai')

@push('styles')
    <style>
        /* Tabel lebih compact */

        table.table-sm th,
        table.table-sm td {
            padding: 0.2rem 0.3rem !important;
            font-size: 0.65rem !important;
            white-space: nowrap;
            vertical-align: middle !important;
        }

        input.form-control-sm {
            font-size: 0.65rem !important;
            padding: 0.15rem 0.25rem !important;
            height: calc(1.3em + 0.3rem + 2px) !important;
        }

        /* Hapus tombol increment/decrement di input number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .table-responsive {
            max-height: 450px;
            overflow-x: auto;
            overflow-y: auto;
            font-family: Arial, sans-serif;
            font-size: 0.7rem;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background-color: #f4f6f9;
            z-index: 2;
        }

        .nowrap-ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
            display: inline-block;
        }

        .absensi-keterangan {
            font-size: 0.7rem;
            margin-bottom: 0.3rem;
            padding: 0.25rem 0.5rem;
            background-color: #e2f0fb;
            border-left: 3px solid #17a2b8;
            line-height: 1.3;
        }

        .absensi-keterangan span.badge {
            margin-right: 0.5rem;
            font-size: 0.7rem;
        }

        /* Nama pegawai tampil biasa tanpa kotak */
        td.text-left.nowrap-ellipsis {
            font-size: 0.6rem !important;
            /* kecilkan font size */
            font-weight: 300 !important;
            padding-left: 0.5rem !important;
            background: transparent !important;
            text-align: left !important;
            border: none !important;
        }

        /* Hilangkan style input di kolom nama jika ada */
        td.text-left.nowrap-ellipsis input {
            display: none !important;
        }

        /* Warna baris ganjil untuk body tabel (striped) */
        .table tbody tr:nth-child(odd) td {
            background-color: rgba(0, 0, 0, 0.02);
            /* ringan, overlay */
        }

        /* Tetap pertahankan warna bulan soft */
        .bulan-pertama {
            background-color: #f9f0f0 !important;
        }

        .bulan-kedua {
            background-color: #f0f9f3 !important;
        }

        .bulan-ketiga {
            background-color: #d4def1 !important;
        }

        /* Striping baris ganjil untuk nama pegawai */
        .table tbody tr:nth-child(odd) td.text-left.nowrap-ellipsis {
            background-color: #f1f1f1;
            /* abu-abu terang, lebih jelas */
        }

        /* Striping baris ganjil untuk kolom bulan */
        tbody tr:nth-child(odd) .bulan-pertama {
            background-color: #f5dada !important;
            /* merah muda soft tapi lebih tegas */
        }

        tbody tr:nth-child(odd) .bulan-kedua {
            background-color: #d9f5e6 !important;
            /* hijau soft tapi lebih tegas */
        }

        tbody tr:nth-child(odd) .bulan-ketiga {
            background-color: #b8cfee !important;
            /* biru soft tapi lebih tegas */
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-2">
        <div class="card card-outline card-info shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-calendar-check mr-1"></i> Input Absensi Pegawai</h3>
                <span class="badge badge-info">TW {{ $tw }}</span>
            </div>

            {{-- Pilih TW --}}
            <div class="card-body p-2">
                <form method="GET" action="{{ route('absensi.index') }}" class="form-inline mb-2">
                    <label class="mr-2">Pilih Triwulan:</label>
                    <select name="tw" class="form-control form-control-sm mr-2">
                        @for ($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}" {{ $tw == $i ? 'selected' : '' }}>TW {{ $i }}
                            </option>
                        @endfor
                    </select>
                    <button class="btn btn-sm btn-info">Tampilkan</button>
                </form>
            </div>

            <form action="{{ route('absensi.store') }}" method="POST">
                @csrf
                <div class="card-body table-responsive p-1">
                    <div class="absensi-keterangan text-center mb-2">
                        <strong>Petunjuk Pengisian:</strong><br>
                        <span class="badge badge-danger">S</span>Sakit
                        <span class="badge badge-warning">I</span>Izin
                        <span class="badge badge-secondary">TK</span>Tanpa Keterangan
                        <span class="badge badge-info">DL</span>Dinas Luar
                        <span class="badge badge-success">C</span>Cuti
                    </div>

                    <table class="table table-bordered table-hover table-striped table-sm nowrap">
                        <thead class="text-center">
                            <tr>
                                <th rowspan="2">Nama Pegawai</th>
                                @foreach ($bulan as $index => $b)
                                    @php
                                        $kelasBulan = '';
                                        if ($index == 0) {
                                            $kelasBulan = 'bulan-pertama';
                                        } elseif ($index == 1) {
                                            $kelasBulan = 'bulan-kedua';
                                        } elseif ($index == 2) {
                                            $kelasBulan = 'bulan-ketiga';
                                        }
                                    @endphp
                                    <th colspan="5" class="{{ $kelasBulan }}">{{ $b }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($bulan as $index => $b)
                                    @php
                                        $kelasBulan = '';
                                        if ($index == 0) {
                                            $kelasBulan = 'bulan-pertama';
                                        } elseif ($index == 1) {
                                            $kelasBulan = 'bulan-kedua';
                                        } elseif ($index == 2) {
                                            $kelasBulan = 'bulan-ketiga';
                                        }
                                    @endphp
                                    @foreach (['S', 'I', 'TK', 'DL', 'C'] as $jenis)
                                        <th class="{{ $kelasBulan }}">{{ $jenis }}</th>
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pegawaiList as $pegawai)
                                <tr>
                                    <td class="text-left nowrap-ellipsis">{{ $pegawai->nama_rekening }}</td>

                                    @foreach ($bulan as $index => $b)
                                        @php
                                            $kelasBulan = '';
                                            if ($index == 0) {
                                                $kelasBulan = 'bulan-pertama';
                                            } elseif ($index == 1) {
                                                $kelasBulan = 'bulan-kedua';
                                            } elseif ($index == 2) {
                                                $kelasBulan = 'bulan-ketiga';
                                            }
                                        @endphp

                                        @foreach (['sakit', 'izin', 'ketidakhadiran', 'dinas_luar', 'cuti'] as $key)
                                            <td class="{{ $kelasBulan }}">
                                                <input type="number"
                                                    name="absensi[{{ $pegawai->id }}][{{ strtolower($b) }}][{{ $key }}]"
                                                    value="" min="0"
                                                    class="form-control form-control-sm text-center" placeholder="0">
                                            </td>
                                        @endforeach
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex align-items-center p-2">
                    <small class="text-muted">Halaman {{ $pegawaiList->currentPage() }} dari
                        {{ $pegawaiList->lastPage() }}</small>
                    <button type="submit" class="btn btn-info btn-sm ml-auto"><i class="fas fa-save"></i> Simpan Semua
                        Absensi</button>
                </div>

            </form>

            <div class="card-footer p-2 pt-0">
                {{ $pegawaiList->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
