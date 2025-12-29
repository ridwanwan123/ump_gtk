@extends('layouts.base')

@section('title', 'Detail Pegawai')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="fw-bold">Detail Pegawai</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('pegawai.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>

                    @can('update', $pegawai)
                        <a href="{{ route('pegawai.edit', $pegawai->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            {{-- CARD HEADER --}}
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                                style="width:80px;height:80px;font-size:32px;">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <h4 class="mb-1">{{ $pegawai->nama_rekening }}</h4>
                            <span class="badge badge-info">{{ $pegawai->jabatan }}</span>
                            <div class="text-muted mt-1">
                                <i class="fas fa-school"></i>
                                {{ $pegawai->madrasah->nama_madrasah ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                {{-- DATA PRIBADI --}}
                <div class="col-md-6">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-id-card"></i> Data Pribadi
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <tr>
                                    <th width="40%">NIK</th>
                                    <td>{{ $pegawai->nik }}</td>
                                </tr>
                                <tr>
                                    <th>Tempat, Tanggal Lahir</th>
                                    <td>
                                        {{ $pegawai->tempat_lahir }},
                                        {{ \Carbon\Carbon::parse($pegawai->tanggal_lahir)->format('d M Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Nama Ibu Kandung</th>
                                    <td>{{ $pegawai->nama_ibu_kandung }}</td>
                                </tr>
                                <tr>
                                    <th>Pendidikan Terakhir</th>
                                    <td>{{ $pegawai->pend_terakhir }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $pegawai->alamat_gtk }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- DATA KEPEGAWAIAN --}}
                <div class="col-md-6">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-briefcase"></i> Data Kepegawaian
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <tr>
                                    <th width="40%">Jabatan</th>
                                    <td>{{ $pegawai->jabatan }}</td>
                                </tr>
                                <tr>
                                    <th>PEG ID</th>
                                    <td>{{ $pegawai->pegid }}</td>
                                </tr>
                                <tr>
                                    <th>Madrasah</th>
                                    <td>{{ $pegawai->madrasah->nama_madrasah ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>NPWP</th>
                                    <td>{{ $pegawai->npwp }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- DATA KONTAK --}}
                <div class="col-md-6">
                    <div class="card card-outline card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-phone"></i> Data Kontak
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <tr>
                                    <th width="40%">No HP</th>
                                    <td>{{ $pegawai->nomor_hp }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $pegawai->alamat_email }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- DATA BANK --}}
                <div class="col-md-6">
                    <div class="card card-outline card-danger">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-university"></i> Data Bank
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <tr>
                                    <th width="40%">No Rekening Bank DKI</th>
                                    <td>{{ $pegawai->no_rek_bank_dki }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
