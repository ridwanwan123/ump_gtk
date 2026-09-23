@extends('layouts.base')

@section('title', 'Detail Pegawai')

@php
    $berkas = [
        'Link Drive Foto KTP' => $pegawai->link_drive_foto_ktp,
        'Link Drive EMIS 4.0' => $pegawai->link_drive_emis40,
        'Link Drive EMIS GTK' => $pegawai->link_drive_emis_gtk,
    ];

    $validasi = [
        'NIK Sesuai' => $pegawai->nik_sesuai,
        'NIK Terdaftar di EMIS 4.0' => $pegawai->nik_terdaftar_emis40,
        'NIK Terdaftar di EMIS GTK' => $pegawai->nik_terdaftar_emis_gtk,
    ];
@endphp

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
                            <h4 class="mb-1">{{ $pegawai->nama_simpatika }}</h4>
                            <span class="badge badge-info">{{ $pegawai->jabatan_ump }}</span>
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
                                    <th width="40%">Nama Simpatika</th>
                                    <td>{{ $pegawai->nama_simpatika ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Rekening</th>
                                    <td>{{ $pegawai->nama_rekening ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>NIK</th>
                                    <td>{{ $pegawai->nik ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tempat, Tanggal Lahir</th>
                                    <td>
                                        {{ $pegawai->tempat_lahir ?? '-' }},
                                        {{ $pegawai->tanggal_lahir ? \Carbon\Carbon::parse($pegawai->tanggal_lahir)->format('d M Y') : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Agama</th>
                                    <td>{{ $pegawai->agama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Ibu Kandung</th>
                                    <td>{{ $pegawai->nama_ibu_kandung ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Pendidikan Terakhir</th>
                                    <td>{{ $pegawai->pend_terakhir ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat Domisili</th>
                                    <td>{{ $pegawai->alamat_gtk ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat Sesuai KTP</th>
                                    <td>{{ $pegawai->alamat_sesuai_ktp ?? '-' }}</td>
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
                                    <th width="40%">Status ASN</th>
                                    <td>
                                        @if ($pegawai->status_asn)
                                            <span class="badge badge-primary">{{ $pegawai->status_asn ?? '-' }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status Pegawai</th>
                                    <td>
                                        @if ($pegawai->status_pegawai)
                                            <span class="badge badge-success">{{ $pegawai->status_pegawai ?? '-' }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Jabatan UMP</th>
                                    <td>{{ $pegawai->jabatan_ump ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jabatan Dinas</th>
                                    <td>{{ $pegawai->jabatan_dinas ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>PEG ID</th>
                                    <td>{{ $pegawai->pegid ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Madrasah</th>
                                    <td>{{ $pegawai->madrasah->nama_madrasah ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>NPSN MADRASAH</th>
                                    <td>{{ $pegawai->npsn_tempat_tugas ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>NPWP</th>
                                    <td>{{ $pegawai->npwp ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dapodik</th>
                                    <td>{{ $pegawai->dapodik ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- BERKAS & VALIDASI --}}
                <div class="col-md-6">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-folder-open"></i> Berkas &amp; Validasi
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                @foreach ($berkas as $label => $link)
                                    <tr>
                                        <th width="40%">{{ $label }}</th>
                                        <td>
                                            @if ($link)
                                                <a href="{{ $link }}" target="_blank" rel="noopener"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-external-link-alt"></i> Lihat Berkas
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach ($validasi as $label => $nilai)
                                    <tr>
                                        <th>{{ $label }}</th>
                                        <td>
                                            @if ($nilai == 'YA')
                                                <span class="badge badge-success"><i class="fas fa-check"></i> YA</span>
                                            @elseif ($nilai == 'TIDAK')
                                                <span class="badge badge-danger"><i class="fas fa-times"></i> TIDAK</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>

                {{-- DATA KONTAK & BANK --}}
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
                                    <td>{{ $pegawai->nomor_hp ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $pegawai->alamat_email ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

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
                                    <td>{{ $pegawai->no_rek_bank_dki ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

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
