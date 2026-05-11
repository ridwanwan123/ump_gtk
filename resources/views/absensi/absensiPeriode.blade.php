{{-- resources/views/pages/attendance-periods/index.blade.php --}}

@extends('layouts.base')

@section('title', 'Periode Absensi')

@push('styles')
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-soft: #e0f2fe;
            --success: #10b981;
            --danger: #ef4444;
            --dark: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --bg: #f8fafc;
        }

        body {
            background: var(--bg);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-title h3 {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .page-title p {
            color: var(--muted);
            margin: 0;
        }

        .btn-primary-soft {
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            border: none;
            color: white;
            padding: 12px 18px;
            border-radius: 14px;
            font-weight: 600;
            box-shadow: 0 10px 20px rgba(14, 165, 233, .18);
            transition: .3s ease;
        }

        .btn-primary-soft:hover {
            transform: translateY(-2px);
            opacity: .95;
        }

        .stats-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .stats-card {
            background: white;
            border-radius: 20px;
            padding: 22px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(15, 23, 42, .04);
        }

        .stats-label {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 8px;
        }

        .stats-value {
            font-size: 30px;
            font-weight: 800;
            color: var(--dark);
        }

        .content-card {
            background: white;
            border-radius: 24px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .04);
        }

        .card-header-custom {
            padding: 22px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header-custom h5 {
            margin: 0;
            font-weight: 700;
            color: var(--dark);
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 10px 14px 10px 42px;
            min-width: 260px;
            outline: none;
            transition: .2s;
        }

        .search-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, .12);
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 12px;
            color: #94a3b8;
        }

        .table-modern {
            margin: 0;
        }

        .table-modern thead {
            background: #f8fafc;
        }

        .table-modern th {
            border: none;
            padding: 18px 24px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #64748b;
            font-weight: 700;
        }

        .table-modern td {
            border-top: 1px solid #f1f5f9;
            padding: 20px 24px;
            vertical-align: middle;
        }

        .period-badge {
            background: var(--primary-soft);
            color: var(--primary);
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .action-group {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .2s ease;
        }

        .btn-edit {
            background: #eff6ff;
            color: #2563eb;
        }

        .btn-delete {
            background: #fef2f2;
            color: #dc2626;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .empty-state {
            padding: 80px 20px;
            text-align: center;
        }

        .empty-state img {
            width: 180px;
            margin-bottom: 20px;
            opacity: .9;
        }

        .empty-state h5 {
            font-weight: 700;
            color: var(--dark);
        }

        .empty-state p {
            color: var(--muted);
        }

        .custom-modal {
            border: none;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(15, 23, 42, .15);
        }

        .modal-header {
            padding: 28px 28px 10px;
        }

        .modal-body {
            padding: 20px 28px;
        }

        .modal-footer {
            padding: 10px 28px 28px;
        }

        .custom-label {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .input-modern {
            position: relative;
        }

        .input-modern i {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 2;
        }

        .custom-input {
            height: 56px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding-left: 48px;
            font-size: 15px;
            transition: .2s ease;
        }

        .custom-input:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, .12);
        }

        .btn-save {
            height: 52px;
            padding: 0 24px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #06b6d4, #0ea5e9);
            color: white;
            font-weight: 700;
            transition: .2s ease;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            color: white;
        }

        .btn-cancel {
            height: 52px;
            padding: 0 20px;
            border-radius: 14px;
            font-weight: 600;
        }

        .active-switch {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 20px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        /* SWITCH */
        .switch {
            position: relative;
            display: inline-block;
            width: 56px;
            height: 30px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: #cbd5e1;
            transition: .3s;
            border-radius: 999px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
        }

        .switch input:checked+.slider {
            background: #0ea5e9;
        }

        .switch input:checked+.slider:before {
            transform: translateX(26px);
        }
    </style>
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">

            <strong>Berhasil!</strong>
            {{ session('success') }}

            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>

        </div>
    @endif
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="page-header">
            <div class="page-title">
                <h3>Periode Absensi</h3>
                <p>Kelola periode absensi peserta berdasarkan tahun dan triwulan.</p>
            </div>

            <button class="btn btn-primary-soft" data-toggle="modal" data-target="#modalCreatePeriod">
                <i class="fas fa-plus mr-2"></i>
                Tambah Periode
            </button>
        </div>

        {{-- STATS --}}
        <div class="stats-wrapper">
            <div class="stats-card">
                <div class="stats-label">Total Periode</div>
                <div class="stats-value">
                    {{ $periods->total() }}
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-label">Periode Aktif</div>
                <div class="stats-value text-success">
                    {{ $periods->where('is_active', true)->count() }}
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-label">Periode Nonaktif</div>
                <div class="stats-value text-danger">
                    {{ $periods->where('is_active', false)->count() }}
                </div>
            </div>

        </div>

        {{-- TABLE --}}
        <div class="content-card">

            <div class="card-header-custom">

                <h5>Daftar Periode</h5>

                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari periode...">
                </div>

            </div>

            @if ($periods->count() > 0)

                <div class="table-responsive">
                    <table class="table table-modern align-middle">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tahun</th>
                                <th>Triwulan</th>
                                <th>Status</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($periods as $period)
                                @php
                                    $bulanMap = [
                                        1 => 'Jan - Mar',
                                        2 => 'Apr - Jun',
                                        3 => 'Jul - Sep',
                                        4 => 'Okt - Des',
                                    ];

                                    $twNumber = (int) str_replace('TW ', '', $period->triwulan);
                                @endphp

                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>
                                        <strong>{{ $period->tahun }}</strong>
                                    </td>
                                    <td>
                                        <span class="period-badge">
                                            <i class="fas fa-calendar-alt"></i>

                                            {{ $period->triwulan }}

                                            <small class="text-muted ml-1">
                                                ({{ $bulanMap[$twNumber] ?? '-' }})
                                            </small>
                                        </span>
                                    </td>
                                    <td>
                                        @if ($period->is_active)
                                            <span class="status-active">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="status-inactive">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('attendance-period.toggle', $period->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <label class="switch">
                                                <input type="checkbox" onchange="this.form.submit()"
                                                    {{ $period->is_active ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>

                <div class="p-4">
                    {{ $periods->links() }}
                </div>
            @else
                <div class="empty-state">

                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486740.png" alt="">

                    <h5>Belum Ada Periode</h5>

                    <p>
                        Tambahkan periode absensi terlebih dahulu agar peserta dapat melakukan absensi.
                    </p>

                </div>

            @endif

        </div>

    </div>

    {{-- MODAL TAMBAH PERIODE --}}
    <div class="modal fade" id="modalCreatePeriod" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content custom-modal">

                {{-- HEADER --}}
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h4 class="modal-title fw-bold mb-1">
                            Tambah Periode
                        </h4>

                        <p class="text-muted mb-0">
                            Buat periode absensi baru untuk peserta.
                        </p>
                    </div>

                    <button type="button" class="close" data-dismiss="modal">

                        <span>&times;</span>
                    </button>
                </div>

                {{-- BODY --}}
                <form action="{{ route('attendance-period.store') }}" method="POST">

                    @csrf

                    <div class="modal-body pt-4">

                        {{-- TAHUN --}}
                        <div class="mb-4">
                            <label class="form-label custom-label">
                                Tahun
                            </label>

                            <div class="input-modern">
                                <i class="fas fa-calendar"></i>

                                <input type="number" name="tahun" class="form-control custom-input"
                                    placeholder="Contoh: 2026" min="2024" max="2100" required>
                            </div>
                        </div>

                        {{-- TRIWULAN --}}
                        <div class="mb-4">
                            <label class="form-label custom-label">
                                Triwulan
                            </label>

                            <div class="input-modern">
                                <i class="fas fa-layer-group"></i>

                                <select name="triwulan" class="form-select custom-input" required>

                                    <option value="">Pilih Triwulan</option>
                                    <option value="TW 1">TW 1</option>
                                    <option value="TW 2">TW 2</option>
                                    <option value="TW 3">TW 3</option>
                                    <option value="TW 4">TW 4</option>

                                </select>
                            </div>
                        </div>

                        {{-- STATUS --}}
                        <div class="active-switch">

                            <div>
                                <h6 class="mb-1 fw-semibold">
                                    Jadikan Periode Aktif
                                </h6>

                                <small class="text-muted">
                                    Jika aktif, peserta dapat memilih periode ini.
                                </small>
                            </div>

                            <label class="switch">
                                <input type="checkbox" name="is_active" value="1">
                                <span class="slider"></span>
                            </label>

                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="modal-footer border-0 pt-0">

                        <button type="button" class="btn btn-light btn-cancel" data-dismiss="modal">

                            Batal
                        </button>

                        <button type="submit" class="btn btn-save">

                            <i class="fas fa-save me-2"></i>
                            Simpan Periode
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>

@endsection
