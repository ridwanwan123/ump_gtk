@extends('layouts.base')

@section('title', 'Data Pegawai')

@push('styles')
<style>
    /* Card Filter */
    .card-filter {
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .card-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }
    .form-control-sm {
        font-size: 0.85rem;
    }
    .table td, .table th {
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
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- FILTER CARD --}}
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
                            @foreach($madrasahs as $madrasah)
                                <option value="{{ $madrasah->id }}" {{ request('madrasah') == $madrasah->id ? 'selected' : '' }}>
                                    {{ $madrasah->nama_madrasah }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Jabatan --}}
                    <div class="col-md-5">
                        <label>Jabatan</label>
                        <select name="jabatan" class="form-control form-control-sm">
                            <option value="">-- Semua Jabatan --</option>
                            @foreach($jabatanList as $jabatan)
                                <option value="{{ $jabatan }}" {{ request('jabatan') == $jabatan ? 'selected' : '' }}>
                                    {{ $jabatan }}
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

    {{-- HEADER & ACTION --}}
    <div class="d-flex justify-content-between mb-3">
        <h2 class="fw-bold">Data Pegawai</h2>
        <div>
            @can('create', App\Models\Pegawai::class)
                <a href="{{ route('pegawai.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Tambah Pegawai</a>
            @endcan
            @can('viewAny', App\Models\Pegawai::class)
                <a href="{{ route('pegawai.export') }}" class="btn btn-success btn-sm"><i class="fas fa-file-export"></i> Export</a>
            @endcan
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" class="mb-3">
        <div class="input-group" style="max-width: 450px;">
            <input type="text" name="search" class="form-control" placeholder="Cari Nama Pegawai..." value="{{ request('search') }}"> 
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Search</button>
        </div>
    </form>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table id="pegawaiTable" class="table table-bordered table-hover table-striped nowrap">
                <thead class="table-light text-center">
                    <tr>
                        <th>No</th>
                        <th>Madrasah</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Nomor HP</th>
                        <th>PEG ID</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pegawais as $i => $pegawai)
                        <tr>
                            <td class="text-center">{{ $pegawais->firstItem() + $i }}</td>
                            <td>{{ $pegawai->madrasah->nama_madrasah ?? '-' }}</td>
                            <td>{{ $pegawai->nama_rekening }}</td>
                            <td>{{ $pegawai->jabatan }}</td>
                            <td>{{ $pegawai->nomor_hp }}</td>
                            <td>{{ $pegawai->pegid }}</td>
                            <td class="text-center">
                                <a href="{{ route('pegawai.show', $pegawai->id) }}" class="btn btn-sm btn-info">Detail</a>
                                @can('delete', $pegawai)
                                    <form action="{{ route('pegawai.destroy', $pegawai->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Apakah anda yakin ingin menghapus pegawai ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $pegawais->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#pegawaiTable').DataTable({
            responsive: true,
            autoWidth: false,
            scrollX: true,
            searching: false, // kita pakai search form custom
            columnDefs: [
                { targets: '_all', whiteSpace: 'nowrap' }
            ]
        });
    });
</script>
@endsection
