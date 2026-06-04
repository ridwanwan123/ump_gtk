@extends('layouts.base')

@section('title', 'Hak Pembayaran Pegawai')

@push('styles')
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #4f46e5;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --bg: #f8fafc;
        }

        .content-wrapper {
            background: var(--bg);
        }

        .saas-card {
            background: white;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
        }

        .period-card {
            border: none;
            border-radius: 14px;
            color: white;
            background: radial-gradient(circle at center,
                    #3b82f6 0%,
                    #1d4ed8 40%,
                    #0f172a 100%);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
        }

        /* efek glow lembut di tengah */
        .period-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center,
                    rgba(255, 255, 255, 0.15),
                    transparent 60%);
            pointer-events: none;
        }

        .period-card h6 {
            color: rgba(255, 255, 255, 0.75);
            font-weight: 600;
        }

        .period-card h3 {
            color: #ffffff;
            font-weight: 800;
        }

        .period-card small {
            color: rgba(255, 255, 255, 0.65);
            font-weight: 500;
        }

        .notice-card {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 18px;
            padding: 18px;
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .notice-icon {
            font-size: 28px;
        }

        .employee-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .employee-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .table-modern thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            border: none;
            padding: 14px;
        }

        .table-modern tbody td {
            vertical-align: middle;
            border-top: 1px solid #f1f5f9;
        }

        .table-modern tbody tr:hover {
            background: #f8fafc;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .month-status {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
        }

        .month-status.active {
            background: #dcfce7;
            color: #166534;
        }

        .month-status.inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn {
            border-radius: 12px;
        }

        .form-control {
            border-radius: 12px;
        }

        .modal-content {
            border: none;
            border-radius: 24px;
        }

        .modal-header {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: white;
            border: none;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">

        @if (auth()->user()->hasRole('superadmin'))
            <div class="card saas-card mb-3">
                <div class="card-body">

                    <h6 class="font-weight-bold mb-3">
                        <i class="fas fa-school text-primary mr-2"></i>
                        Filter Madrasah
                    </h6>

                    <form method="GET" action="{{ route('hak-pembayaran.index') }}">
                        <div class="d-flex">

                            <select name="madrasah" class="form-control mr-2" onchange="this.form.submit()">

                                <option value="">Semua Madrasah</option>

                                @foreach ($madrasahList as $m)
                                    <option value="{{ $m->id }}"
                                        {{ request('madrasah') == $m->id ? 'selected' : '' }}>
                                        {{ $m->nama_madrasah }}
                                    </option>
                                @endforeach

                            </select>

                            @if (request()->filled('madrasah'))
                                <a href="{{ route('hak-pembayaran.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                            @endif

                        </div>
                    </form>

                </div>
            </div>
        @endif

        <div class="card saas-card mb-3 period-card">

            <div class="card-body text-center">

                <div class="text-bold">
                    Periode Aktif
                </div>

                <h2 class="font-weight-bold mb-2">
                    {{ $activePeriod->triwulan }} - {{ $activePeriod->tahun }}
                </h2>

                <div class="text-bold">
                    Periode ini ditentukan oleh administrator sistem.
                </div>

            </div>

        </div>

        {{-- INFO --}}
        <div class="notice-card">

            <div class="notice-icon">
                ⚠️
            </div>

            <div>
                <strong>Perhatian</strong>

                <div>
                    Hak pembayaran menentukan perhitungan honor pegawai dan tidak bergantung pada jumlah absensi.
                    Pastikan data yang disimpan sudah sesuai.
                </div>
            </div>

        </div>

        {{-- ACTION --}}
        <div class="d-flex justify-content-end align-items-center mb-3">

            <button class="btn btn-primary shadow-sm px-4 py-2" data-toggle="modal" data-target="#modalCreate">

                <i class="fas fa-layer-group mr-2"></i>
                Kelola Hak Pembayaran
            </button>
        </div>

        {{-- TABLE --}}
        <div class="card saas-card">

            <div class="card-body table-responsive p-0">

                <table class="table table-modern text-center mb-0">

                    <thead class="text-uppercase">

                        <tr>
                            <th width="60">No</th>
                            <th class="text-left">NAMA PEGAWAI</th>

                            @foreach ($bulan as $b)
                                <th>
                                    {{ \Carbon\Carbon::create()->month($b)->format('M') }}
                                </th>
                            @endforeach
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($pegawai as $p)

                            <tr>

                                <td>{{ $loop->iteration }}</td>

                                <td class="text-left">

                                    <div class="employee-cell">

                                        <div class="employee-avatar">
                                            {{ strtoupper(substr($p->nama_rekening, 0, 1)) }}
                                        </div>

                                        <div>
                                            <div class="font-weight-bold">
                                                {{ $p->nama_rekening }}
                                            </div>

                                            <small class="text-muted">
                                                {{ optional($p->madrasah)->nama_madrasah }}
                                            </small>
                                        </div>

                                    </div>

                                </td>

                                @foreach ($bulan as $b)
                                    @php
                                        $status = $hakMap[$p->id][$b] ?? 0;
                                    @endphp

                                    <td>

                                        @if ($status)
                                            <div class="month-status active">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        @else
                                            <div class="month-status inactive">
                                                <i class="fas fa-times"></i>
                                            </div>
                                        @endif

                                    </td>
                                @endforeach

                            </tr>

                        @empty

                            <tr>
                                <td colspan="10">
                                    Tidak ada data pegawai
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if (auth()->user()->hasRole('superadmin'))
                <div class="card-footer">
                    {{ $pegawai->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>

    </div>

    {{-- MODAL CREATE --}}
    <div class="modal fade" id="modalCreate" tabindex="-1">
        <div class="modal-dialog modal-xl">

            <form method="POST" action="{{ route('hak-pembayaran.store') }}">
                @csrf

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">🧾 Input Hak Pembayaran Pegawai</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="alert alert-info">

                            <strong>Periode Aktif:</strong>
                            {{ $activePeriod->triwulan }}
                            -
                            {{ $activePeriod->tahun }}

                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">

                                <thead class="text-uppercase">
                                    <tr>
                                        <th>#</th>
                                        <th>NAMA PEGAWAI</th>

                                        @foreach ($bulan as $b)
                                            <th>
                                                <div class="d-flex flex-column align-items-center">

                                                    <small class="mb-1">
                                                        {{ \Carbon\Carbon::create()->month($b)->format('M') }}
                                                    </small>

                                                    <input type="checkbox" class="check-all-month"
                                                        data-month="{{ $b }}">
                                                </div>
                                            </th>
                                        @endforeach

                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($pegawai as $p)
                                        <tr data-pegawai="{{ $p->id }}">
                                            <td>{{ $loop->iteration }}</td>

                                            <td class="text-start fw-bold">
                                                {{ $p->nama_rekening }}
                                            </td>

                                            @foreach ($bulan as $b)
                                                @php
                                                    $checked = $hakMap[$p->id][$b] ?? 0;
                                                @endphp

                                                <td>
                                                    <input type="hidden"
                                                        name="hak[{{ $p->id }}][{{ $b }}]"
                                                        value="0">

                                                    <input type="checkbox"
                                                        class="form-check-input month-checkbox month-{{ $b }}"
                                                        name="hak[{{ $p->id }}][{{ $b }}]"
                                                        value="1" {{ $checked ? 'checked' : '' }}>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success">💾 Simpan Semua</button>
                    </div>

                </div>
            </form>

        </div>
    </div>

@endsection

@push('scripts')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    <script>
        $('form').on('submit', function(e) {

            let valid = true;

            $('tr[data-pegawai]').each(function() {

                let checked = $(this).find('input[type=checkbox]:checked').length;

                if (checked === 0) {
                    valid = false;
                    $(this).css('background', '#fee2e2');
                } else {
                    $(this).css('background', '');
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Minimal pilih 1 bulan untuk setiap pegawai');
            }
        });

        $('.check-all-month').on('change', function() {
            let month = $(this).data('month');
            let checked = $(this).is(':checked');

            $('.month-' + month).prop('checked', checked);
        });
    </script>
@endpush
