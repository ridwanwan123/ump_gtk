@extends('layouts.base')

@section('title', 'Data Pegawai')

@section('content')
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0 fw-bold">Data Pegawai</h1>
            @can('create', App\Models\Pegawai::class)
                <a href="{{ route('pegawai.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Tambah Pegawai
                </a>
            @endcan
            @can('viewAny', App\Models\Pegawai::class)
                <a href="{{ route('pegawai.export') }}" class="btn btn-success">
                    Export Pegawai
                </a>
            @endcan
        </div>
    </div>


    <form method="GET" class="mb-3">
        <div class="input-group" style="max-width: 300px;">
            <input type="text" name="search" class="form-control" placeholder="Cari Pegawai..."
                value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>

    <section class="content">
        <div class="container-fluid">

            {{-- Alert --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Table --}}
            <div class="card shadow-sm border-0">
                <div class="card-body table-responsive">
                    <table id="pegawaiTable" class="table table-bordered table-hover table-striped nowrap"
                        style="width:100%">
                        <thead class="table-light">
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
                            @foreach ($pegawais as $index => $pegawai)
                                <tr>
                                    <td>{{ $pegawais->firstItem() + $index }}</td>
                                    <td>{{ $pegawai->nama_rekening }}</td>
                                    <td>{{ $pegawai->jabatan }}</td>
                                    <td>{{ $pegawai->madrasah->nama_madrasah ?? '-' }}</td>
                                    <td>{{ $pegawai->nomor_hp }}</td>
                                    <td>{{ $pegawai->pegid }}</td>
                                    <td>
                                        <a href="{{ route('pegawai.show', $pegawai->id) }}"
                                            class="btn btn-sm btn-info">Detail</a>

                                        @can('delete', $pegawai)
                                            <form action="{{ route('pegawai.destroy', $pegawai->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Apakah anda yakin ingin menghapus pegawai ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
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
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#pegawaiTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "scrollX": true,
                "searching": true, // <-- ini enable search bar
                "columnDefs": [{
                    "white-space": "nowrap",
                    "targets": "_all"
                }]
            });
        });
    </script>
@endsection
