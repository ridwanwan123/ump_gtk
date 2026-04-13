@extends('layouts.base')

@section('title', 'Data Usulan Pegawai')

@push('styles')
    <style>
        /* Card Filter */
        .card-filter {
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .form-control-sm {
            font-size: 0.85rem;
        }

        .table td,
        .table th {
            vertical-align: middle;
            white-space: nowrap;
        }

        .badge-role {
            font-size: 0.75rem;
            padding: 0.35em 0.6em;
        }

        .btn-action {
            padding: 0.25rem 0.45rem;
        }

        .dataTables_wrapper .dataTables_filter {
            float: right;
            text-align: right;
        }

        .alert-custom {
            background: #fff;
            border-left: 4px solid #f0ad4e;
            border-radius: 8px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- FILTER CARD --}}
        @role('superadmin')
            <div class="card card-info  card-outline mb-3 shadow-sm card-filter">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Data Pegawai</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <form method="GET" action="">
                        <div class="row">
                            {{-- Madrasah --}}
                            <div class="col-md-5">
                                <label>Madrasah</label>
                                <select name="madrasah" class="form-control form-control-sm">
                                    <option value="">-- Semua Madrasah --</option>
                                    @foreach ($madrasahs as $madrasah)
                                        <option value="{{ $madrasah->id }}"
                                            {{ request('madrasah') == $madrasah->id ? 'selected' : '' }}>
                                            {{ $madrasah->nama_madrasah }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Jabatan --}}
                            <div class="col-md-5">
                                <label>Jabatan</label>
                                <select name="jabatan_ump" class="form-control form-control-sm">
                                    <option value="">-- Semua Jabatan --</option>
                                    @foreach ($jabatanList as $jabatan_ump)
                                        <option value="{{ $jabatan_ump }}"
                                            {{ request('jabatan_ump') == $jabatan_ump ? 'selected' : '' }}>
                                            {{ $jabatan_ump }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Button Filter --}}
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-info btn-sm btn-block">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endrole

        @role('operator')
            <div class="alert alert-custom shadow-sm mb-3 d-flex align-items-start">
                <div class="me-2 text-warning">
                    <i class="fas fa-info-circle mr-1"></i>
                </div>
                <div>
                    <div class="fw-semibold mb-1">Pengajuan Pengusulan Pegawai</div>
                    <div class="text-muted small">
                        Pegawai tidak langsung diusulkan, tetapi akan berstatus
                        <span class="badge bg-primary">Usulan</span>
                        dan diproses setelah persetujuan Kanwil.
                    </div>
                </div>
            </div>
        @endrole

        {{-- HEADER & ACTION --}}
        <div class="d-flex justify-content-between mb-3">
            <h2 class="fw-bold">Data Pengusulan Pegawai</h2>
            <div>
                <a href="{{ route('pengusulan-pegawai.create') }}" class="btn btn-primary btn-sm"><i
                        class="fas fa-plus"></i> Pengajuan Usulan Pegawai</a>

            </div>
        </div>

        {{-- TABLE --}}
        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">
                <table id="pegawaiTable" class="table table-bordered table-hover table-striped nowrap">
                    <thead class="table-light text-center">
                        <tr>
                            <th>NO</th>
                            <th>MADRASAH</th>
                            <th>NAMA SIMPATIKA</th>
                            <th>JABATAN</th>
                            <th>NIK</th>
                            <th>PEG ID</th>
                            <th>NPWP</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pegawaiUsulan as $index => $pegawai)
                            <tr>
                                <td class="text-center">
                                    {{ $pegawaiUsulan->firstItem() + $index }}
                                </td>
                                <td>{{ $pegawai->madrasah->nama_madrasah ?? '-' }}</td>
                                <td>{{ $pegawai->nama_rekening }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info badge-role">
                                        {{ $pegawai->jabatan_ump }}
                                    </span>
                                </td>
                                <td>{{ $pegawai->nik ?? '-' }}</td>
                                <td>{{ $pegawai->pegid ?? '-' }}</td>
                                <td>{{ $pegawai->npwp ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('pengusulan-pegawai.show', $pegawai->id) }}"
                                        class="btn btn-sm btn-info btn-action">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Tidak ada data pegawai usulan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $pegawaiUsulan->links('pagination::bootstrap-5') }}
                </div>
            </div>
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
    @endpush

@endsection
