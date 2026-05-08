@extends('layouts.base')

@section('title', 'Rekap Honor Pegawai')

@push('styles')
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #16a34a;
            --danger: #dc2626;
            --dark: #0f172a;
            --gray: #64748b;
            --border: #e2e8f0;
            --bg: #f1f5f9;
        }

        body {
            background: var(--bg);
        }

        /* =========================
                                                           PAGE
                                        ========================= */

        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .page-subtitle {
            color: var(--gray);
            font-size: 14px;
        }

        /* =========================
                                                           CARD
                                                        ========================= */

        .modern-card {
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            background: white;
            box-shadow:
                0 10px 30px rgba(15, 23, 42, .06),
                0 2px 6px rgba(15, 23, 42, .04);
        }

        .modern-card-header {
            padding: 18px 24px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .modern-card-header h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .modern-card-body {
            padding: 24px;
        }

        /* =========================
                                                           INFO BOX
                                                        ========================= */

        .info-box-modern {
            background: linear-gradient(135deg, #eff6ff, #f8fafc);
            border: 1px solid #bfdbfe;
            border-radius: 18px;
            padding: 22px;
            margin-bottom: 24px;
        }

        .info-box-modern h5 {
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 14px;
        }

        .info-box-modern ul {
            margin-bottom: 0;
            padding-left: 18px;
        }

        .info-box-modern li {
            margin-bottom: 8px;
            color: #334155;
        }

        /* =========================
                                                           FORM
                                                        ========================= */

        .form-label-modern {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-control,
        .select2-container--default .select2-selection--multiple {
            border-radius: 12px !important;
            border: 1px solid #dbeafe !important;
            min-height: 46px;
            box-shadow: none !important;
        }

        .form-control:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .12) !important;
        }

        .btn-modern {
            border: 0;
            border-radius: 12px;
            height: 46px;
            font-weight: 600;
            transition: .25s;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-primary-modern:hover {
            transform: translateY(-1px);
            color: white;
            box-shadow: 0 8px 20px rgba(37, 99, 235, .25);
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
        }

        .btn-success-modern:hover {
            transform: translateY(-1px);
            color: white;
            box-shadow: 0 8px 20px rgba(22, 163, 74, .25);
        }

        /* =========================
                                                           SUMMARY
                                                        ========================= */

        .summary-card {
            background: white;
            border-radius: 18px;
            padding: 20px;
            box-shadow:
                0 10px 30px rgba(15, 23, 42, .06),
                0 2px 6px rgba(15, 23, 42, .04);
            height: 100%;
        }

        .summary-label {
            color: var(--gray);
            font-size: 13px;
            margin-bottom: 6px;
        }

        .summary-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--dark);
        }

        /* =========================
                                                           TABLE
                                                        ========================= */

        .table-wrapper {
            border-radius: 20px;
            overflow: hidden;
            background: white;
            box-shadow:
                0 10px 30px rgba(15, 23, 42, .06),
                0 2px 6px rgba(15, 23, 42, .04);
        }

        .table-modern {
            margin-bottom: 0;
            font-size: 13px;
        }

        .table-modern thead tr:first-child th {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            color: #1e3a8a;
            border: 1px solid #bfdbfe;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .4px;
            font-weight: 700;
        }

        .table-modern thead tr:nth-child(2) th {
            background: #f8fafc;
            color: #334155;
            border: 1px solid #e2e8f0;
            font-size: 12px;
            font-weight: 700;
        }

        .table-modern th,
        .table-modern td {
            padding: 10px 12px;
            border-color: #e2e8f0;
            vertical-align: middle;
        }

        .table-modern tbody tr {
            transition: .2s;
        }

        .table-modern tbody tr:hover {
            background: #f8fafc;
        }

        .table-modern tbody td:first-child {
            position: sticky;
            left: 0;
            background: white;
            z-index: 2;
            font-weight: 700;
            color: var(--dark);
        }

        .badge-soft {
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-success-soft {
            background: #dcfce7;
            color: #166534;
        }

        .badge-danger-soft {
            background: #fee2e2;
            color: #991b1b;
        }

        .grand-total {
            background: #f0fdf4 !important;
            color: #166534;
            font-weight: 800;
        }

        .money {
            font-weight: 700;
            color: var(--dark);
        }

        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">

        {{-- PAGE HEADER --}}
        <div class="mb-4">

            <div class="page-title">
                Rekap Honor Pegawai
            </div>

            <div class="page-subtitle">
                Monitoring honorarium, kehadiran, dan potongan pegawai
            </div>

        </div>

        {{-- INFO --}}
        <div class="info-box-modern">

            <h5>
                <i class="fas fa-circle-info mr-1"></i>
                Informasi Perhitungan
            </h5>

            <ul>
                <li>Honor dihitung berdasarkan jumlah bulan yang dipilih.</li>
                <li>Sakit (S) tidak dikenakan potongan.</li>
                <li>Izin (I) dipotong 2.5% dari honor bulanan.</li>
                <li>Tanpa Keterangan (TK) dipotong 5% dari honor bulanan.</li>
                <li>Jumlah bersih dihitung otomatis setelah potongan.</li>
            </ul>

        </div>

        {{-- FILTER --}}
        <div class="modern-card mb-4">

            <div class="modern-card-header">

                <h4>
                    <i class="fas fa-filter mr-2"></i>
                    Filter Rekap Honor
                </h4>

            </div>

            <div class="modern-card-body">

                <form method="GET">

                    <div class="row">

                        {{-- TAHUN --}}
                        <div class="col-md-3 mb-3">

                            <label class="form-label-modern">
                                Tahun
                            </label>

                            <input type="number" name="tahun" class="form-control"
                                value="{{ request('tahun', date('Y')) }}">

                        </div>

                        {{-- BULAN --}}
                        <div class="col-md-5 mb-3">

                            <label class="form-label-modern">
                                Pilih Bulan
                            </label>

                            <select name="bulan[]" class="form-control select2" multiple required>

                                @foreach ([
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ] as $key => $val)
                                    <option value="{{ $key }}"
                                        {{ collect(request('bulan'))->contains($key) ? 'selected' : '' }}>
                                        {{ $val }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                        {{-- HONOR --}}
                        <div class="col-md-4 mb-3">

                            <label class="form-label-modern">
                                Honor per Bulan
                            </label>

                            <input type="number" name="honor" class="form-control" placeholder="Contoh: 3900000"
                                value="{{ request('honor') }}" required>

                        </div>

                    </div>

                    <div class="row">

                        {{-- MADRASAH --}}
                        <div class="col-md-4 mb-3">

                            <label class="form-label-modern">
                                Madrasah
                            </label>

                            <select name="madrasah" class="form-control">

                                <option value="">Semua Madrasah</option>

                                @foreach ($madrasahs as $m)
                                    <option value="{{ $m->id }}"
                                        {{ request('madrasah') == $m->id ? 'selected' : '' }}>

                                        {{ $m->nama_madrasah }}

                                    </option>
                                @endforeach

                            </select>

                        </div>

                        {{-- JABATAN --}}
                        <div class="col-md-4 mb-3">

                            <label class="form-label-modern">
                                Jabatan
                            </label>

                            <select name="jabatan_ump" class="form-control">

                                <option value="">Semua Jabatan</option>

                                @foreach ($jabatanList as $j)
                                    <option value="{{ $j }}"
                                        {{ request('jabatan_ump') == $j ? 'selected' : '' }}>

                                        {{ $j }}

                                    </option>
                                @endforeach

                            </select>

                        </div>

                        {{-- BUTTON --}}
                        <div class="col-md-4 d-flex align-items-end mb-3">

                            <button type="submit" class="btn btn-modern btn-primary-modern w-100">

                                <i class="fas fa-chart-line mr-2"></i>
                                Tampilkan Rekap

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        {{-- EXPORT --}}
        @if (request()->has('bulan') && request('tahun') && request('honor') && !empty($data))
            <div class="mb-3">

                <a href="{{ route('rekap-honor.export', request()->all()) }}" class="btn btn-success-modern btn-modern">

                    <i class="fas fa-file-excel mr-2"></i>
                    Export Excel

                </a>

            </div>
        @endif

        {{-- HASIL --}}
        @if (!empty($data))

            {{-- SUMMARY --}}
            <div class="row mb-4">

                <div class="col-md-4 mb-3">

                    <div class="summary-card">

                        <div class="summary-label">
                            Total Pegawai
                        </div>

                        <div class="summary-value">
                            {{ count($data) }}
                        </div>

                    </div>

                </div>

                <div class="col-md-4 mb-3">

                    <div class="summary-card">

                        <div class="summary-label">
                            Total Bulan Dipilih
                        </div>

                        <div class="summary-value">
                            {{ count(request('bulan', [])) }}
                        </div>

                    </div>

                </div>

                <div class="col-md-4 mb-3">

                    <div class="summary-card">

                        <div class="summary-label">
                            Honor per Bulan
                        </div>

                        <div class="summary-value">
                            Rp {{ number_format(request('honor'), 0, ',', '.') }}
                        </div>

                    </div>

                </div>

            </div>

            {{-- TABLE --}}
            <div class="table-wrapper">

                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="mb-0 font-weight-bold">
                            Rekap Honor Pegawai
                        </h5>

                        <small class="text-muted">
                            Data honorarium dan ketidakhadiran pegawai
                        </small>

                    </div>

                    <span class="badge badge-success-soft badge-soft">
                        {{ count($data) }} Pegawai
                    </span>

                </div>

                <div class="table-responsive">

                    <table class="table table-modern table-bordered text-nowrap align-middle">

                        <thead class="text-center">

                            {{-- HEADER 1 --}}
                            <tr>

                                <th rowspan="2" style="vertical-align: middle;">NAMA PEGAWAI</th>

                                {{-- BULAN --}}
                                @foreach (request('bulan', []) as $bulan)
                                    <th colspan="5">

                                        {{ strtoupper(\Carbon\Carbon::create()->month($bulan)->translatedFormat('F')) }}

                                    </th>
                                @endforeach

                                <th colspan="6">JUMLAH KETIDAK HADIRAN</th>

                                <th rowspan="2" style="vertical-align: middle;">BANYAK BULAN</th>
                                <th rowspan="2" style="vertical-align: middle;">% KEHADIRAN</th>
                                <th rowspan="2" style="vertical-align: middle;">HONOR / BULAN</th>
                                <th rowspan="2" style="vertical-align: middle;">JUMLAH KOTOR</th>

                                <th colspan="3">% & POTONGAN</th>

                                <th rowspan="2" style="vertical-align: middle;">TOTAL POTONGAN</th>
                                <th rowspan="2" style="vertical-align: middle;">SETELAH POTONGAN</th>
                                <th rowspan="2" style="vertical-align: middle;">PPH</th>
                                <th rowspan="2" style="vertical-align: middle;">BERSIH</th>

                            </tr>

                            {{-- HEADER 2 --}}
                            <tr>

                                {{-- SUB BULAN --}}
                                @foreach (request('bulan', []) as $bulan)
                                    <th>S</th>
                                    <th>I</th>
                                    <th>TK</th>
                                    <th>DL</th>
                                    <th>C</th>
                                @endforeach

                                {{-- TOTAL --}}
                                <th>S</th>
                                <th>I</th>
                                <th>TK</th>
                                <th>DL</th>
                                <th>C</th>
                                <th>JML</th>

                                {{-- POTONGAN --}}
                                <th>0%</th>
                                <th>2.5%</th>
                                <th>5%</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($data as $row)
                                <tr class="text-center">

                                    {{-- NAMA --}}
                                    <td class="text-start">

                                        {{ $row['nama'] }}

                                    </td>

                                    {{-- DETAIL PER BULAN --}}
                                    @foreach (request('bulan', []) as $bulan)
                                        @php
                                            $d = $row['detail_bulan'][$bulan];
                                        @endphp

                                        <td>{{ $d['s'] }}</td>
                                        <td>{{ $d['i'] }}</td>
                                        <td>{{ $d['tk'] }}</td>
                                        <td>{{ $d['dl'] }}</td>
                                        <td>{{ $d['c'] }}</td>
                                    @endforeach

                                    {{-- TOTAL --}}
                                    <td>{{ $row['total_s'] }}</td>
                                    <td>{{ $row['total_i'] }}</td>
                                    <td>{{ $row['total_tk'] }}</td>
                                    <td>{{ $row['total_dl'] }}</td>
                                    <td>{{ $row['total_c'] }}</td>

                                    <td>

                                        {{ $row['total_s'] + $row['total_i'] + $row['total_tk'] + $row['total_dl'] + $row['total_c'] }}

                                    </td>

                                    {{-- BASIC --}}
                                    <td>

                                        {{ $row['banyak_bulan'] }}

                                    </td>

                                    {{-- KEHADIRAN --}}
                                    <td>

                                        @if ($row['persen_kehadiran'] >= 90)
                                            <span class="badge badge-success-soft badge-soft">
                                                {{ $row['persen_kehadiran'] }}%
                                            </span>
                                        @else
                                            <span class="badge badge-danger-soft badge-soft">
                                                {{ $row['persen_kehadiran'] }}%
                                            </span>
                                        @endif

                                    </td>

                                    {{-- HONOR --}}
                                    <td class="money">

                                        Rp {{ number_format($row['jumlah_honor_per_bulan'], 0, ',', '.') }}

                                    </td>

                                    {{-- KOTOR --}}
                                    <td class="money">

                                        Rp {{ number_format($row['jumlah_kotor'], 0, ',', '.') }}

                                    </td>

                                    {{-- POTONGAN --}}
                                    <td>

                                        Rp {{ number_format($row['potongan_s'], 0, ',', '.') }}

                                    </td>

                                    <td>

                                        Rp {{ number_format($row['potongan_i'], 0, ',', '.') }}

                                    </td>

                                    <td>

                                        Rp {{ number_format($row['potongan_tk'], 0, ',', '.') }}

                                    </td>

                                    {{-- FINAL --}}
                                    <td class="money">

                                        Rp {{ number_format($row['total_potongan'], 0, ',', '.') }}

                                    </td>

                                    <td class="money">

                                        Rp {{ number_format($row['setelah_potongan'], 0, ',', '.') }}

                                    </td>

                                    <td>

                                        Rp {{ number_format($row['pph'], 0, ',', '.') }}

                                    </td>

                                    <td class="grand-total">

                                        Rp {{ number_format($row['jumlah_bersih'], 0, ',', '.') }}

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        @endif

    </div>

@endsection
