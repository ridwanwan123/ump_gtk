@extends('layouts.base')

@section('title', 'Penonaktifan Pegawai')

@push('styles')
    <style>
        .table td,
        .table th {
            vertical-align: middle;
            white-space: nowrap;
        }

        .btn-action {
            padding: 0.25rem 0.45rem;
        }

        .alert-custom {
            background: #fff;
            border-left: 4px solid #f0ad4e;
            border-radius: 8px;
        }

        .nav-link {
            color: inherit;
            /* Warna mengikuti parent */
            text-decoration: none;
            /* Hilangkan underline */
            cursor: default;
            /* Hapus icon pointer jika ingin seperti teks */
            background-color: transparent;
            /* Tidak ada latar */
        }

        .nav-link.active {
            font-weight: bold;
            /* Bisa ditebalkan jika aktif */
        }

        .nav-link:hover {
            color: inherit;
            /* tetap sama warna */
            text-decoration: none;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- ALERT --}}
        <div class="alert alert-custom shadow-sm mb-3 d-flex align-items-start">
            <div class="me-2 text-warning">
                <i class="fas fa-info-circle mr-1"></i>
            </div>
            <div>
                <div class="fw-semibold mb-1">Pengajuan Penonaktifan Pegawai</div>
                <div class="text-muted small">
                    Data tidak langsung dinonaktifkan, tetapi akan berstatus
                    <span class="badge bg-secondary">Pending</span>
                    dan diproses setelah persetujuan Kanwil.
                </div>
            </div>
        </div>

        {{-- TAB --}}
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link active" href="#aktif" data-toggle="tab">
                    Pegawai Aktif
                    <span class="badge bg-light text-dark">{{ $pegawaiAktif->total() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#pending" data-toggle="tab">
                    Pending
                    <span class="badge bg-light text-dark">{{ $pegawaiPending->total() }}</span>
                </a>
            </li>
        </ul>

        <div class="tab-content">

            {{-- ================= AKTIF ================= --}}
            <div class="tab-pane fade show active" id="aktif">
                <div class="card shadow-sm border-0">
                    <div class="card-body table-responsive">

                        <table class="table table-bordered table-hover table-striped">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Madrasah</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>NIK</th>
                                    <th>PEG ID</th>
                                    <th>NPWP</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pegawaiAktif as $i => $p)
                                    <tr>
                                        <td class="text-center">{{ $pegawaiAktif->firstItem() + $i }}</td>
                                        <td>{{ $p->madrasah?->nama_madrasah ?? '-' }}</td>
                                        <td>{{ $p->nama_simpatika }}</td>
                                        <td>{{ $p->jabatan_ump }}</td>
                                        <td>{{ $p->nik }}</td>
                                        <td>{{ $p->pegid }}</td>
                                        <td>{{ $p->npwp }}</td>
                                        <td class="text-center">

                                            <a href="{{ route('pegawai.show', $p->id) }}"
                                                class="btn btn-sm btn-info btn-action">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <form action="{{ route('pegawai.destroy', $p->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Ajukan penonaktifan pegawai ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger btn-action">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $pegawaiAktif->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

            {{-- ================= PENDING ================= --}}
            <div class="tab-pane fade" id="pending">
                <div class="card shadow-sm border-0">
                    <div class="card-body table-responsive">

                        <table class="table table-bordered table-hover table-striped">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Madrasah</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>NIK</th>
                                    <th>PEG ID</th>
                                    <th>NPWP</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pegawaiPending as $i => $p)
                                    <tr>
                                        <td class="text-center">{{ $pegawaiPending->firstItem() + $i }}</td>
                                        <td>{{ $p->madrasah?->nama_madrasah ?? '-' }}</td>
                                        <td>{{ $p->nama_simpatika }}</td>
                                        <td>{{ $p->jabatan_ump }}</td>
                                        <td>{{ $p->nik }}</td>
                                        <td>{{ $p->pegid }}</td>
                                        <td>{{ $p->npwp }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">Pending</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $pegawaiPending->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        @if (session('swal_success'))
            <script>
                Swal.fire({
                    title: 'Berhasil',
                    text: "{{ session('swal_success') }}",
                    icon: 'success',
                    confirmButtonColor: '#0D47A1'
                });
            </script>
        @endif
    @endpush

@endsection
