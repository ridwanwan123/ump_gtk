@extends('layouts.base')

@section('title', 'Data Pegawai')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 fw-bold">Data Pegawai</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            {{-- Alert --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Table --}}
            <div class="card shadow-sm border-0">
                <div class="card-body table-responsive">
                    <table id="pegawaiTable" class="table table-bordered table-hover table-striped nowrap"
                        style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Madrasah</th>
                                <th>No HP</th>
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
                                    <td>
                                        <a href="{{ route('pegawai.show', $pegawai->id) }}"
                                            class="btn btn-sm btn-info">Detail</a>

                                        @can('update', $pegawai)
                                            <a href="{{ route('pegawai.edit', $pegawai->id) }}"
                                                class="btn btn-sm btn-warning">Edit</a>
                                        @endcan

                                        @can('delete', $pegawai)
                                            <form action="{{ route('pegawai.destroy', $pegawai->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Yakin hapus data?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">Hapus</button>
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
                "scrollX": true, // untuk nowrap & scroll horizontal
                "columnDefs": [{
                        "white-space": "nowrap",
                        "targets": "_all"
                    } // paksa semua kolom nowrap
                ]
            });
        });
    </script>
@endsection
