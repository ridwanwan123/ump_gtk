@extends('layouts.base')

@section('title', 'Tambah Pegawai')

@section('content')
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="fw-bold">Tambah Pegawai</h1>
                <small class="text-muted">Isi semua data pegawai dengan benar</small>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('pegawai.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <form action="{{ route('pegawai.store') }}" method="POST">
            @csrf

            <div class="row">

                {{-- KIRI: DATA PRIBADI --}}
                <div class="col-md-6">
                    <div class="card card-outline card-info h-100">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-id-card"></i> Data Pribadi
                            </h3>
                        </div>

                        <div class="card-body">

                            {{-- Nama Pegawai --}}
                            <div class="form-group">
                                <label>Nama Pegawai</label>
                                <input type="text" name="nama_rekening"
                                    class="form-control @error('nama_rekening') is-invalid @enderror"
                                    value="{{ old('nama_rekening') }}">
                                @error('nama_rekening')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- NIK --}}
                            <div class="form-group">
                                <label>NIK</label>
                                <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                                    value="{{ old('nik') }}">
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tempat & Tanggal Lahir --}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir"
                                        class="form-control @error('tempat_lahir') is-invalid @enderror"
                                        value="{{ old('tempat_lahir') }}">
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir"
                                        class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                        value="{{ old('tanggal_lahir') }}">
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Nama Ibu Kandung --}}
                            <div class="form-group">
                                <label>Nama Ibu Kandung</label>
                                <input type="text" name="nama_ibu_kandung"
                                    class="form-control @error('nama_ibu_kandung') is-invalid @enderror"
                                    value="{{ old('nama_ibu_kandung') }}">
                                @error('nama_ibu_kandung')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Pendidikan Terakhir --}}
                            <div class="form-group">
                                <label>Pendidikan Terakhir</label>
                                <input type="text" name="pend_terakhir"
                                    class="form-control @error('pend_terakhir') is-invalid @enderror"
                                    value="{{ old('pend_terakhir') }}">
                                @error('pend_terakhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Alamat --}}
                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="alamat_gtk" class="form-control @error('alamat_gtk') is-invalid @enderror" rows="4">{{ old('alamat_gtk') }}</textarea>
                                @error('alamat_gtk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- KANAN: DATA KEPEGAWAIAN, KONTAK, BANK --}}
                <div class="col-md-6">

                    {{-- Data Kepegawaian --}}
                    <div class="card card-outline card-success mb-3">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-briefcase"></i> Data Kepegawaian</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label>Jabatan</label>
                                <input type="text" name="jabatan"
                                    class="form-control @error('jabatan') is-invalid @enderror"
                                    value="{{ old('jabatan') }}">
                                @error('jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>PEG ID</label>
                                <input type="text" name="pegid"
                                    class="form-control @error('pegid') is-invalid @enderror" value="{{ old('pegid') }}">
                                @error('pegid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Madrasah</label>
                                <select name="id_madrasah" class="form-control @error('id_madrasah') is-invalid @enderror">
                                    <option value="">-- Pilih Madrasah --</option>
                                    @foreach ($madrasah as $m)
                                        <option value="{{ $m->id }}"
                                            {{ old('id_madrasah') == $m->id ? 'selected' : '' }}>
                                            {{ $m->nama_madrasah }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_madrasah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-0">
                                <label>NPWP</label>
                                <input type="text" name="npwp"
                                    class="form-control @error('npwp') is-invalid @enderror" value="{{ old('npwp') }}">
                                @error('npwp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Data Kontak --}}
                    <div class="card card-outline card-warning mb-3">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-phone"></i> Data Kontak</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label>No HP</label>
                                <input type="text" name="nomor_hp"
                                    class="form-control @error('nomor_hp') is-invalid @enderror"
                                    value="{{ old('nomor_hp') }}">
                                @error('nomor_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-0">
                                <label>Email</label>
                                <input type="email" name="alamat_email"
                                    class="form-control @error('alamat_email') is-invalid @enderror"
                                    value="{{ old('alamat_email') }}">
                                @error('alamat_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Data Bank --}}
                    <div class="card card-outline card-danger">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-university"></i> Data Bank</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group mb-0">
                                <label>No Rekening Bank DKI</label>
                                <input type="text" name="no_rek_bank_dki"
                                    class="form-control @error('no_rek_bank_dki') is-invalid @enderror"
                                    value="{{ old('no_rek_bank_dki') }}">
                                @error('no_rek_bank_dki')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            {{-- ACTION --}}
            <div class="card mt-3">
                <div class="card-body text-right">
                    <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </div>

        </form>
    </div>
@endsection
