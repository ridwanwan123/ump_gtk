@extends('layouts.base')

@section('title', 'Data Absensi Pegawai')

@push('styles')
    <style>
        /* ------------------------------
                                                                           Filter dan Form
                                                                        -------------------------------*/
        .form-group label {
            font-weight: 600;
            font-size: 0.85rem;
        }

        .form-control-sm {
            font-size: 0.8rem;
            height: calc(1.5em + 0.5rem + 2px);
        }

        .card.card-info.card-outline {
            border-radius: 0.35rem;
        }

        /* ------------------------------
                                                                           Tabel
                                                                        -------------------------------*/
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

        /* Striping */
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

        /* Badge TW */
        .badge-triwulan {
            font-size: 0.8rem;
            background-color: #17a2b8;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-2">

        {{-- FILTER CARD --}}
        <div class="card card-info card-outline mb-3 shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Data Absensi</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-2">
                <form method="GET" action="{{ route('absensi.index') }}">
                    @php
                        $currentYear = now()->year;
                        $currentMonth = now()->month;
                        $defaultTW = ceil($currentMonth / 3); // otomatis TW sesuai bulan
                        $selectedYear = request('tahun', $currentYear);
                        $selectedTW = request('tw', $defaultTW);
                    @endphp

                    <div class="form-row align-items-end">
                        {{-- Tahun --}}
                        <div class="form-group col-md-5">
                            <label for="tahun">Tahun</label>
                            <select name="tahun" id="tahun" class="form-control form-control-sm">
                                @for ($y = $currentYear; $y >= $currentYear - 5; $y--)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- Triwulan --}}
                        <div class="form-group col-md-5">
                            <label for="tw">Triwulan</label>
                            <select name="tw" id="tw" class="form-control form-control-sm">
                                @for ($i = 1; $i <= 4; $i++)
                                    <option value="{{ $i }}" {{ $selectedTW == $i ? 'selected' : '' }}>
                                        TW {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- Tombol Filter --}}
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-info btn-sm btn-block">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="card card-outline card-info shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-calendar-check mr-1"></i> Data Absensi Pegawai</h3>
                <span class="badge badge-triwulan">TRIWULAN {{ $tw }}</span>
            </div>

            <div class="card-body table-responsive p-1">
                <table class="table table-bordered table-hover table-sm nowrap">
                    <thead class="text-center">
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Madrasah</th>
                            <th rowspan="2">Nama Pegawai</th>

                            <th colspan="5" class="bulan-pertama">{{ $bulanTriwulan[0] }}</th>
                            <th colspan="5" class="bulan-kedua">{{ $bulanTriwulan[1] }}</th>
                            <th colspan="5" class="bulan-ketiga">{{ $bulanTriwulan[2] }}</th>
                        </tr>
                        <tr class="font-weight-bold">
                            @foreach (['S', 'I', 'TK', 'DL', 'C'] as $h)
                                <th class="bulan-pertama sub-header">{{ $h }}</th>
                            @endforeach
                            @foreach (['S', 'I', 'TK', 'DL', 'C'] as $h)
                                <th class="bulan-kedua sub-header">{{ $h }}</th>
                            @endforeach
                            @foreach (['S', 'I', 'TK', 'DL', 'C'] as $h)
                                <th class="bulan-ketiga sub-header">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($pegawaiList as $i => $pegawai)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $pegawai->nama_madrasah }}</td>
                                <td class="nama-pegawai">{{ $pegawai->nama_rekening }}</td>

                                {{-- Bulan 1 --}}
                                <td class="text-center bulan-pertama">{{ $pegawai->s }}</td>
                                <td class="text-center bulan-pertama">{{ $pegawai->i }}</td>
                                <td class="text-center bulan-pertama">{{ $pegawai->kt }}</td>
                                <td class="text-center bulan-pertama">{{ $pegawai->dl }}</td>
                                <td class="text-center bulan-pertama">{{ $pegawai->c }}</td>

                                {{-- Bulan 2 --}}
                                <td class="text-center bulan-kedua">0</td>
                                <td class="text-center bulan-kedua">0</td>
                                <td class="text-center bulan-kedua">0</td>
                                <td class="text-center bulan-kedua">0</td>
                                <td class="text-center bulan-kedua">0</td>

                                {{-- Bulan 3 --}}
                                <td class="text-center bulan-ketiga">0</td>
                                <td class="text-center bulan-ketiga">0</td>
                                <td class="text-center bulan-ketiga">0</td>
                                <td class="text-center bulan-ketiga">0</td>
                                <td class="text-center bulan-ketiga">0</td>
                            </tr>
                        @endforeach
                    </tbody>

                    {{-- FOOTER TOTAL --}}
                    <tfoot class="text-center font-weight-bold">
                        <tr>
                            <td colspan="3">JUMLAH KESELURUHAN ABSENSI</td>

                            {{-- Bulan 1 --}}
                            <td class="bulan-pertama">{{ $totalAbsensi['s'] }}</td>
                            <td class="bulan-pertama">{{ $totalAbsensi['i'] }}</td>
                            <td class="bulan-pertama">{{ $totalAbsensi['kt'] }}</td>
                            <td class="bulan-pertama">{{ $totalAbsensi['dl'] }}</td>
                            <td class="bulan-pertama">{{ $totalAbsensi['c'] }}</td>

                            {{-- Bulan 2 --}}
                            <td class="bulan-kedua">0</td>
                            <td class="bulan-kedua">0</td>
                            <td class="bulan-kedua">0</td>
                            <td class="bulan-kedua">0</td>
                            <td class="bulan-kedua">0</td>

                            {{-- Bulan 3 --}}
                            <td class="bulan-ketiga">0</td>
                            <td class="bulan-ketiga">0</td>
                            <td class="bulan-ketiga">0</td>
                            <td class="bulan-ketiga">0</td>
                            <td class="bulan-ketiga">0</td>
                        </tr>
                    </tfoot>

                </table>

            </div>
        </div>

    </div>
@endsection
