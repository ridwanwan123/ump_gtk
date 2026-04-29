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
            text-decoration: none;
            cursor: default;
            background-color: transparent;
        }

        .nav-link.active {
            font-weight: bold;
        }

        .nav-link:hover {
            color: inherit;
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
                    Pegawai tidak langsung dinonaktifkan, tetapi akan berstatus
                    <span class="badge bg-warning">Proses Non Aktif</span>
                    dan diproses setelah persetujuan Kanwil.
                </div>
            </div>
        </div>

        {{-- TAB --}}
        <ul class="nav nav-tabs mb-3">

            <li class="nav-item">
                <a class="nav-link active" href="#pending" data-toggle="tab">
                    Pegawai Proses NonAktif
                    <span class="badge bg-light text-dark">{{ $pegawaiPending->total() }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#aktif" data-toggle="tab">
                    Pegawai Aktif
                    <span class="badge bg-light text-dark">{{ $pegawaiAktif->total() }}</span>
                </a>
            </li>
        </ul>

        <div class="tab-content">

            {{-- ================= PROSES NON AKTIF ================= --}}
            <div class="tab-pane fade show active" id="pending">
                <div class="card shadow-sm border-0">
                    <div class="card-body table-responsive">

                        <table class="table table-bordered table-hover table-striped">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>MADRASAH</th>
                                    <th>NAMA SIMPATIKA</th>
                                    <th>JABATAN</th>
                                    <th>STATUS</th>
                                    <th>ALASAN</th>
                                    <th>TANGGAL NONAKTIF</th>
                                    @role('superadmin')
                                        <th>AKSI</th>
                                    @endrole
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pegawaiPending as $i => $p)
                                    <tr>
                                        <td class="text-center">{{ $pegawaiPending->firstItem() + $i }}</td>
                                        <td>{{ $p->madrasah?->nama_madrasah ?? '-' }}</td>
                                        <td>{{ $p->nama_simpatika }}</td>
                                        <td>{{ $p->jabatan_ump }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">Pengajuan Non Aktif</span>
                                        </td>
                                        <td>{{ $p->alasan_mengundurkan_diri ?? '-' }}</td>
                                        <td>
                                            {{ $p->tgl_nonaktif ? \Carbon\Carbon::parse($p->tgl_nonaktif)->translatedFormat('d F Y') : '-' }}
                                        </td>
                                        @role('superadmin')
                                            <td class="text-center">

                                                {{-- SETUJUI --}}
                                                <form action="{{ route('penonaktifan-pegawai.nonaktif', $p->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="button"
                                                        class="btn btn-sm btn-success btn-action btn-confirm-setujui">
                                                        <i class="fas fa-check"></i> Setujui
                                                    </button>
                                                </form>

                                                {{-- TOLAK --}}
                                                <form action="{{ route('penonaktifan-pegawai.tolak', $p->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger btn-action btn-confirm-tolak">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </button>
                                                </form>

                                            </td>
                                        @endrole
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $pegawaiPending->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

            {{-- ================= AKTIF ================= --}}
            <div class="tab-pane fade " id="aktif">
                <div class="card shadow-sm border-0">
                    <div class="card-body table-responsive">

                        <table class="table table-bordered table-hover table-striped">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
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

                                            <a href="{{ route('penonaktifan-pegawai.show', $p->id) }}"
                                                class="btn btn-sm btn-info btn-action">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <form action="{{ route('penonaktifan-pegawai.proses', $p->id) }}"
                                                method="POST" class="d-inline form-proses">
                                                @csrf
                                                <button type="button"
                                                    class="btn btn-sm btn-danger btn-action btn-confirm-proses">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // SETUJUI
            document.querySelectorAll('.btn-confirm-setujui').forEach(btn => {
                btn.addEventListener('click', function() {
                    let form = this.closest('form');

                    Swal.fire({
                        title: 'Setujui Pengajuan?',
                        text: "Pegawai akan dinonaktifkan.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, setujui',
                        cancelButtonText: 'Batal'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            // TOLAK
            document.querySelectorAll('.btn-confirm-tolak').forEach(btn => {
                btn.addEventListener('click', function() {
                    let form = this.closest('form');

                    Swal.fire({
                        title: 'Tolak Pengajuan?',
                        text: "Pegawai akan tetap aktif.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545', // warna danger merah
                        cancelButtonColor: '#adb5bd',
                        confirmButtonText: 'Ya, tolak',
                        cancelButtonText: 'Batal'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            // ================= PROSES =================
            document.querySelectorAll('.btn-confirm-proses').forEach(function(btn) {
                btn.addEventListener('click', function() {

                    let form = this.closest('form');

                    Swal.fire({
                        title: 'Ajukan Penonaktifan?',
                        html: `
                                <div style="text-align:left">

                                    <label style="font-weight:600; font-size:13px; margin-bottom:6px; display:block;">
                                        Alasan Penonaktifan
                                    </label>

                                    <select id="alasan" class="form-control" style="margin-bottom:14px; border-radius:6px;">
                                        <option value="">-- Pilih Alasan --</option>
                                        <option>MENINGGAL DUNIA</option>
                                        <option>PENSIUN</option>
                                        <option>DIANGKAT P3K</option>
                                        <option>DIANGKAT CPNS/PNS</option>
                                        <option>PINDAH KERJA</option>
                                        <option>MENGUNDURKAN DIRI</option>
                                        <option>LAINNYA</option>
                                    </select>

                                    <label style="font-weight:600; font-size:13px; margin-bottom:6px; display:block;">
                                        Tanggal Nonaktif
                                    </label>

                                    <input type="date"
                                        id="tgl_nonaktif"
                                        class="form-control"
                                        style="border-radius:6px;">

                                </div>
                                `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, ajukan!',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            let alasan = document.getElementById('alasan').value;
                            let tgl = document.getElementById('tgl_nonaktif').value;

                            if (!alasan) {
                                Swal.showValidationMessage('Alasan wajib dipilih');
                                return false;
                            }

                            if (!tgl) {
                                Swal.showValidationMessage(
                                    'Tanggal nonaktif wajib diisi');
                                return false;
                            }

                            return {
                                alasan: alasan,
                                tgl_nonaktif: tgl
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {

                            // alasan
                            let inputAlasan = document.createElement('input');
                            inputAlasan.type = 'hidden';
                            inputAlasan.name = 'alasan_mengundurkan_diri';
                            inputAlasan.value = result.value.alasan;

                            // tanggal
                            let inputTgl = document.createElement('input');
                            inputTgl.type = 'hidden';
                            inputTgl.name = 'tgl_nonaktif';
                            inputTgl.value = result.value.tgl_nonaktif;

                            form.appendChild(inputAlasan);
                            form.appendChild(inputTgl);

                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

@endsection
