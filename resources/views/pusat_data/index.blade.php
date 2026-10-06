@extends('layouts.base')

@section('title', 'Pusat Data')

@php
    // Label yang lebih mudah dibaca untuk nilai di database
    $labelStatus = [
        \App\Models\Pegawai::AKTIF => 'Aktif',
        \App\Models\Pegawai::PROSES_NON_AKTIF => 'Sedang Proses Nonaktif',
        \App\Models\Pegawai::NON_AKTIF => 'Nonaktif',
        'NON_AKTIF' => 'Nonaktif', // penulisan lama yang masih ada di database
        \App\Models\Pegawai::PROSES_USULAN => 'Sedang Proses Usulan',
        \App\Models\Pegawai::USULAN => 'Usulan Baru',
        \App\Models\Pegawai::DITOLAK => 'Usulan Ditolak',
    ];
    $labelJabatanDinas = [
        'PENDIDIK' => 'Pendidik (Guru)',
        'TENDIK' => 'Tenaga Kependidikan',
    ];

    $opsiStatus = collect($statusList)->mapWithKeys(fn($s) => [$s => $labelStatus[$s] ?? $s])->all();
    $opsiJenjang = $jenjangList->mapWithKeys(fn($j) => [$j => $j])->all();
    $opsiJabatanDinas = $jabatanDinasList->mapWithKeys(fn($j) => [$j => $labelJabatanDinas[$j] ?? $j])->all();
    $opsiJabatanUmp = $jabatanUmpList->mapWithKeys(fn($j) => [$j => \Illuminate\Support\Str::title($j)])->all();
    $opsiPendidikan = $pendidikanList->mapWithKeys(fn($p) => [$p => $p])->all();

    // Tab pensiun dibuka kembali kalau export pensiun gagal / tidak ada datanya
    $tabPensiun = old('range_tahun') !== null || $errors->has('range_tahun');
    $rangeTahun = (int) old('range_tahun', 3);

    $pilih = fn($key) => array_map('strval', (array) request($key, []));
    $pilihLama = fn($key) => array_map('strval', (array) old($key, []));
@endphp

@push('styles')
    <style>
        .pd-intro {
            color: #6c757d;
            margin-bottom: 1.25rem;
        }

        /* ---------- Tab ---------- */
        .pd-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 1.25rem;
        }

        .pd-tabs .nav-link {
            border: 0;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            font-size: 1rem;
            color: #6c757d;
            cursor: pointer;
            border-radius: 0;
            background: transparent;
        }

        .pd-tabs .nav-link:hover {
            color: #1e3c72;
        }

        .pd-tabs .nav-link.active {
            color: #1e3c72;
            border-bottom-color: #1e3c72;
            background: transparent;
        }

        /* ---------- Kartu & langkah ---------- */
        .pd-card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        }

        .pd-step {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .pd-step-no {
            flex: 0 0 32px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #1e3c72;
            color: #fff;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
        }

        .pd-step-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #212529;
            margin: 0;
        }

        .pd-step-sub {
            font-size: 0.875rem;
            color: #6c757d;
            margin: 0;
        }

        /* ---------- Baris filter ---------- */
        .pd-field {
            padding: 0.9rem 0;
            border-top: 1px solid #f0f1f3;
        }

        .pd-field:first-of-type {
            border-top: 0;
            padding-top: 0;
        }

        .pd-label {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 0.15rem;
        }

        .pd-hint {
            font-size: 0.8rem;
            color: #868e96;
        }

        /* ---------- Tombol pilihan (chip) ---------- */
        .chip-group {
            display: flex;
            flex-wrap: wrap;
            margin: -4px;
        }

        .chip-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            margin: 4px;
            padding: 0.4rem 0.9rem;
            border: 1px solid #ced4da;
            border-radius: 999px;
            background: #fff;
            color: #343a40;
            font-size: 0.9rem;
            font-weight: 500 !important;
            line-height: 1.3;
            cursor: pointer;
            user-select: none;
            transition: all 0.15s ease;
        }

        .chip .fa-check {
            display: none;
            font-size: 0.7rem;
            margin-right: 0.4rem;
        }

        .chip:hover {
            border-color: #1e3c72;
            color: #1e3c72;
        }

        .chip-input:focus-visible+.chip,
        button.chip:focus-visible {
            outline: 2px solid #80bdff;
            outline-offset: 2px;
        }

        .chip-input:checked+.chip,
        .chip.aktif {
            background: #1e3c72;
            border-color: #1e3c72;
            color: #fff;
        }

        .chip-input:checked+.chip .fa-check,
        .chip.aktif .fa-check {
            display: inline-block;
        }

        /* ---------- Dropdown madrasah (select2) ---------- */
        .pd-select+.select2-container {
            width: 100% !important;
            max-width: 480px;
        }

        .pd-select+.select2-container .select2-selection--single {
            height: 40px;
            border: 1px solid #ced4da;
            border-radius: 8px;
        }

        .pd-select+.select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 12px;
            padding-right: 44px;
            color: #343a40;
        }

        .pd-select+.select2-container .select2-selection--single .select2-selection__arrow {
            height: 38px;
            right: 6px;
        }

        .pd-select+.select2-container .select2-selection--single .select2-selection__clear {
            height: 38px;
            margin-right: 24px;
            color: #868e96;
        }

        .select2-dropdown {
            border-color: #ced4da;
            border-radius: 8px;
            overflow: hidden;
        }

        .select2-container--default .select2-results__option {
            color: #343a40;
            padding: 8px 12px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected],
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #1e3c72;
            color: #fff;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 6px 10px;
            color: #343a40;
        }

        /* ---------- Ringkasan & tombol unduh ---------- */
        .pd-summary {
            background: #f4f7fb;
            border: 1px solid #dfe6f1;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .pd-summary-count {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e3c72;
            line-height: 1.1;
        }

        .pd-summary-label {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .pd-summary.berubah .pd-summary-count {
            color: #adb5bd;
        }

        .pd-berubah-note {
            display: none;
            font-size: 0.85rem;
            color: #b7791f;
            margin-top: 0.25rem;
        }

        .pd-summary.berubah .pd-berubah-note {
            display: block;
        }

        .pd-actions .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1rem;
            margin: 4px 0 4px 8px;
        }

        .btn-unduh {
            background: #1d8a4e;
            border-color: #1d8a4e;
            color: #fff;
        }

        .btn-unduh:hover {
            background: #176f3f;
            border-color: #176f3f;
            color: #fff;
        }

        /* ---------- Tabel pratinjau ---------- */
        .pd-table th {
            background: #f8f9fa;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            color: #495057;
            white-space: nowrap;
        }

        .pd-table td {
            vertical-align: middle;
            white-space: nowrap;
        }

        .pd-status {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            background: #e9ecef;
            color: #495057;
        }

        .pd-status.aktif {
            background: #d9f2e3;
            color: #14693a;
        }

        .pagination {
            margin: 0;
        }

        /* ---------- Pensiun ---------- */
        .pd-range {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .pd-range-input {
            width: 150px;
            margin: 4px 0 4px 8px;
        }

        .pd-range-input .form-control {
            height: 38px;
            padding: 0.375rem 0.5rem;
            text-align: center;
            font-weight: 600;
        }

        .pd-info {
            background: #fff8e6;
            border: 1px solid #ffe2a8;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: #6b4e00;
            font-size: 0.9rem;
        }

        @media (max-width: 576px) {
            .pd-actions {
                width: 100%;
                margin-top: 0.75rem;
            }

            .pd-actions .btn {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        <h2 class="font-weight-bold mb-1">Pusat Data</h2>
        <p class="pd-intro">
            Unduh data pegawai dalam bentuk file Excel. Pilih data yang dibutuhkan, lalu tekan tombol
            <strong>Unduh Excel</strong>.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- TAB --}}
        <ul class="nav pd-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $tabPensiun ? '' : 'active' }}" data-toggle="tab" href="#tab-pegawai" role="tab">
                    <i class="fas fa-users mr-1"></i> Data Pegawai
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabPensiun ? 'active' : '' }}" data-toggle="tab" href="#tab-pensiun" role="tab">
                    <i class="fas fa-user-clock mr-1"></i> Data Pensiun
                </a>
            </li>
        </ul>

        <div class="tab-content">

            {{-- ================= DATA PEGAWAI ================= --}}
            <div class="tab-pane fade {{ $tabPensiun ? '' : 'show active' }}" id="tab-pegawai" role="tabpanel">
                <form method="GET" action="{{ route('admin.pusat-data.index') }}" id="form-pegawai">

                    <div class="card pd-card mb-3">
                        <div class="card-body">
                            <div class="pd-step">
                                <div class="pd-step-no">1</div>
                                <div>
                                    <p class="pd-step-title">Pilih data yang ingin diunduh</p>
                                    <p class="pd-step-sub">
                                        Boleh pilih lebih dari satu. Kalau dibiarkan <strong>Semua</strong>, seluruh data
                                        ikut diunduh.
                                    </p>
                                </div>
                            </div>

                            <div class="pd-field row">
                                <div class="col-md-3">
                                    <div class="pd-label">Status Pegawai</div>
                                    <div class="pd-hint">Masih aktif, nonaktif, atau usulan</div>
                                </div>
                                <div class="col-md-9">
                                    @include('pusat_data._chips', [
                                        'name' => 'status',
                                        'prefix' => 'f-status',
                                        'options' => $opsiStatus,
                                        'selected' => $pilih('status'),
                                    ])
                                </div>
                            </div>

                            <div class="pd-field row">
                                <div class="col-md-3">
                                    <div class="pd-label">Jenjang Madrasah</div>
                                    <div class="pd-hint">MIN, MTsN, atau MAN</div>
                                </div>
                                <div class="col-md-9">
                                    @include('pusat_data._chips', [
                                        'name' => 'jenjang',
                                        'prefix' => 'f-jenjang',
                                        'options' => $opsiJenjang,
                                        'selected' => $pilih('jenjang'),
                                    ])
                                </div>
                            </div>

                            <div class="pd-field row">
                                <div class="col-md-3">
                                    <div class="pd-label">Madrasah</div>
                                    <div class="pd-hint">Ketik nama untuk mencari</div>
                                </div>
                                <div class="col-md-9">
                                    <select name="madrasah" class="form-control pd-select"
                                        data-placeholder="Semua Madrasah">
                                        <option value=""></option>
                                        @foreach ($madrasahList as $madrasah)
                                            <option value="{{ $madrasah->id }}"
                                                {{ (string) request('madrasah') === (string) $madrasah->id ? 'selected' : '' }}>
                                                {{ $madrasah->nama_madrasah }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="pd-field row">
                                <div class="col-md-3">
                                    <div class="pd-label">Kelompok Pegawai</div>
                                    <div class="pd-hint">Guru atau tenaga kependidikan</div>
                                </div>
                                <div class="col-md-9">
                                    @include('pusat_data._chips', [
                                        'name' => 'jabatan_dinas',
                                        'prefix' => 'f-dinas',
                                        'options' => $opsiJabatanDinas,
                                        'selected' => $pilih('jabatan_dinas'),
                                    ])
                                </div>
                            </div>

                            <div class="pd-field row">
                                <div class="col-md-3">
                                    <div class="pd-label">Jabatan</div>
                                    <div class="pd-hint">Jabatan UMP pegawai</div>
                                </div>
                                <div class="col-md-9">
                                    @include('pusat_data._chips', [
                                        'name' => 'jabatan_ump',
                                        'prefix' => 'f-ump',
                                        'options' => $opsiJabatanUmp,
                                        'selected' => $pilih('jabatan_ump'),
                                    ])
                                </div>
                            </div>

                            <div class="pd-field row">
                                <div class="col-md-3">
                                    <div class="pd-label">Pendidikan Terakhir</div>
                                </div>
                                <div class="col-md-9">
                                    @include('pusat_data._chips', [
                                        'name' => 'pend_terakhir',
                                        'prefix' => 'f-pend',
                                        'options' => $opsiPendidikan,
                                        'selected' => $pilih('pend_terakhir'),
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card pd-card mb-3">
                        <div class="card-body">
                            <div class="pd-step">
                                <div class="pd-step-no">2</div>
                                <div>
                                    <p class="pd-step-title">Unduh datanya</p>
                                    <p class="pd-step-sub">
                                        File Excel berisi seluruh kolom data pegawai (NIK, rekening, alamat, dan lainnya).
                                    </p>
                                </div>
                            </div>

                            <div class="pd-summary" id="ringkasan-pegawai">
                                <div>
                                    <div class="pd-summary-count">
                                        {{ number_format($totalPegawai, 0, ',', '.') }} pegawai
                                    </div>
                                    <div class="pd-summary-label">sesuai pilihan yang sedang ditampilkan</div>
                                    <div class="pd-berubah-note">
                                        <i class="fas fa-info-circle"></i>
                                        Pilihan berubah. Tekan <strong>Tampilkan Data</strong> untuk melihat jumlah
                                        terbaru, atau langsung unduh.
                                    </div>
                                </div>
                                <div class="pd-actions">
                                    <a href="{{ route('admin.pusat-data.index') }}" class="btn btn-link text-secondary">
                                        Atur Ulang
                                    </a>
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search mr-1"></i> Tampilkan Data
                                    </button>
                                    {{-- Unduh memakai pilihan yang sedang terisi di form --}}
                                    <button type="submit" class="btn btn-unduh"
                                        formaction="{{ route('admin.pusat-data.export-pegawai') }}">
                                        <i class="fas fa-download mr-1"></i> Unduh Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- PRATINJAU --}}
                <div class="card pd-card mb-4">
                    <div class="card-body">
                        <p class="pd-step-title mb-1">Contoh data</p>
                        <p class="pd-step-sub mb-3">
                            Sebagian kolom saja, untuk memastikan pilihannya sudah benar. File Excel berisi kolom lengkap.
                        </p>

                        <div class="table-responsive">
                            <table class="table table-hover pd-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Madrasah</th>
                                        <th>Jenjang</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Pendidikan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pegawai as $index => $p)
                                        <tr>
                                            <td class="text-center">{{ $pegawai->firstItem() + $index }}</td>
                                            <td>{{ $p->madrasah->nama_madrasah ?? '-' }}</td>
                                            <td>{{ $p->madrasah->type ?? '-' }}</td>
                                            <td>{{ $p->nama_rekening }}</td>
                                            <td>{{ $p->jabatan_ump ? \Illuminate\Support\Str::title($p->jabatan_ump) : '-' }}
                                            </td>
                                            <td>{{ $p->pend_terakhir ?? '-' }}</td>
                                            <td>
                                                <span
                                                    class="pd-status {{ $p->status_pegawai === \App\Models\Pegawai::AKTIF ? 'aktif' : '' }}">
                                                    {{ $labelStatus[$p->status_pegawai] ?? ($p->status_pegawai ?? '-') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                Tidak ada pegawai yang sesuai pilihan. Coba kurangi pilihannya.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $pegawai->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= DATA PENSIUN ================= --}}
            <div class="tab-pane fade {{ $tabPensiun ? 'show active' : '' }}" id="tab-pensiun" role="tabpanel">
                <form method="GET" action="{{ route('admin.pusat-data.export-pensiun') }}">

                    <div class="card pd-card mb-3">
                        <div class="card-body">
                            <div class="pd-step">
                                <div class="pd-step-no">1</div>
                                <div>
                                    <p class="pd-step-title">Pensiun dalam berapa tahun ke depan?</p>
                                    <p class="pd-step-sub">
                                        Usia pensiun: guru (pendidik) {{ \App\Models\Pegawai::USIA_PENSIUN_PENDIDIK }}
                                        tahun, pegawai lainnya {{ \App\Models\Pegawai::USIA_PENSIUN_LAINNYA }} tahun.
                                        Hanya pegawai yang masih aktif.
                                    </p>
                                </div>
                            </div>

                            <div class="pd-range mb-3">
                                <div class="chip-group" id="range-cepat">
                                    @foreach ([1, 2, 3, 5, 10] as $tahun)
                                        <button type="button"
                                            class="chip {{ $rangeTahun === $tahun ? 'aktif' : '' }}"
                                            data-tahun="{{ $tahun }}">
                                            <i class="fas fa-check"></i> {{ $tahun }} tahun
                                        </button>
                                    @endforeach
                                </div>
                                <span class="ml-3 text-muted">atau isi sendiri:</span>
                                <div class="input-group pd-range-input">
                                    <input type="number" name="range_tahun" id="range_tahun" class="form-control"
                                        min="1" max="40" value="{{ $rangeTahun }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">tahun</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pd-info">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Akan diunduh: pegawai yang memasuki usia pensiun
                                <strong>sampai dengan <span id="batas-pensiun"></span></strong>.
                            </div>

                            <div class="custom-control custom-checkbox mt-3">
                                <input type="checkbox" class="custom-control-input" id="termasuk_lewat"
                                    name="termasuk_lewat" value="1" checked>
                                <label class="custom-control-label font-weight-normal" for="termasuk_lewat">
                                    Sertakan juga pegawai aktif yang <strong>sudah melewati</strong> usia pensiun
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card pd-card mb-3">
                        <div class="card-body">
                            <div class="pd-step">
                                <div class="pd-step-no">2</div>
                                <div>
                                    <p class="pd-step-title">Persempit data <span
                                            class="font-weight-normal text-muted">(boleh dilewati)</span></p>
                                    <p class="pd-step-sub">Kalau dibiarkan <strong>Semua</strong>, seluruh madrasah ikut
                                        diunduh.</p>
                                </div>
                            </div>

                            <div class="pd-field row">
                                <div class="col-md-3">
                                    <div class="pd-label">Jenjang Madrasah</div>
                                </div>
                                <div class="col-md-9">
                                    @include('pusat_data._chips', [
                                        'name' => 'jenjang',
                                        'prefix' => 'p-jenjang',
                                        'options' => $opsiJenjang,
                                        'selected' => $pilihLama('jenjang'),
                                    ])
                                </div>
                            </div>

                            <div class="pd-field row">
                                <div class="col-md-3">
                                    <div class="pd-label">Madrasah</div>
                                    <div class="pd-hint">Ketik nama untuk mencari</div>
                                </div>
                                <div class="col-md-9">
                                    <select name="madrasah" class="form-control pd-select"
                                        data-placeholder="Semua Madrasah">
                                        <option value=""></option>
                                        @foreach ($madrasahList as $madrasah)
                                            <option value="{{ $madrasah->id }}"
                                                {{ (string) old('madrasah') === (string) $madrasah->id ? 'selected' : '' }}>
                                                {{ $madrasah->nama_madrasah }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="pd-field row">
                                <div class="col-md-3">
                                    <div class="pd-label">Kelompok Pegawai</div>
                                </div>
                                <div class="col-md-9">
                                    @include('pusat_data._chips', [
                                        'name' => 'jabatan_dinas',
                                        'prefix' => 'p-dinas',
                                        'options' => $opsiJabatanDinas,
                                        'selected' => $pilihLama('jabatan_dinas'),
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card pd-card mb-4">
                        <div class="card-body">
                            <div class="pd-step">
                                <div class="pd-step-no">3</div>
                                <div>
                                    <p class="pd-step-title">Unduh datanya</p>
                                    <p class="pd-step-sub">
                                        File Excel diurutkan dari yang paling dekat pensiun, lengkap dengan usia, tanggal
                                        pensiun, dan sisa waktunya.
                                    </p>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-unduh"
                                style="border-radius: 8px; font-weight: 600; padding: 0.5rem 1rem;">
                                <i class="fas fa-download mr-1"></i> Unduh Excel Data Pensiun
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            $(function() {
                // ---------- Dropdown madrasah ----------
                $('.pd-select').each(function() {
                    $(this).select2({
                        placeholder: $(this).data('placeholder'),
                        allowClear: true,
                        width: '100%',
                        language: {
                            noResults: function() {
                                return 'Madrasah tidak ditemukan';
                            }
                        }
                    });
                });

                // ---------- Tombol pilihan: "Semua" aktif selama tidak ada yang dicentang ----------
                $('.chip-group').each(function() {
                    var $group = $(this);
                    var $semua = $group.find('.chip-semua');
                    var $inputs = $group.find('.chip-input');

                    if (!$semua.length) return;

                    $inputs.on('change', function() {
                        $semua.toggleClass('aktif', $inputs.filter(':checked').length === 0);
                    });

                    $semua.on('click', function() {
                        $inputs.prop('checked', false).first().trigger('change');
                        $semua.addClass('aktif');
                    });
                });

                // ---------- Tandai ringkasan kalau pilihan berubah & belum ditampilkan ----------
                $('#form-pegawai').on('change', 'input, select', function() {
                    $('#ringkasan-pegawai').addClass('berubah');
                });

                // ---------- Pensiun: pilihan cepat & keterangan tanggal ----------
                var bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                    'September', 'Oktober', 'November', 'Desember'
                ];
                var $range = $('#range_tahun');

                function perbaruiBatas() {
                    var tahun = parseInt($range.val(), 10);

                    $('#range-cepat .chip').each(function() {
                        $(this).toggleClass('aktif', $(this).data('tahun') === tahun);
                    });

                    if (!tahun || tahun < 1) {
                        $('#batas-pensiun').text('-');
                        return;
                    }

                    var batas = new Date();
                    batas.setFullYear(batas.getFullYear() + tahun);
                    $('#batas-pensiun').text(batas.getDate() + ' ' + bulan[batas.getMonth()] + ' ' + batas
                        .getFullYear());
                }

                $('#range-cepat .chip').on('click', function() {
                    $range.val($(this).data('tahun'));
                    perbaruiBatas();
                });

                $range.on('input change', perbaruiBatas);
                perbaruiBatas();
            });
        </script>

        @if (session('swal_error'))
            <script>
                Swal.fire({
                    title: 'Tidak ada data',
                    text: "{{ session('swal_error') }}",
                    icon: 'info',
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#1e3c72'
                });
            </script>
        @endif
    @endpush

@endsection
