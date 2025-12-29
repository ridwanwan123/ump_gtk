@extends('layouts.base')

@section('title', 'Edit Pegawai')

@section('content')
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="fw-bold">Edit Pegawai</h1>
                <small class="text-muted">Perbarui data pegawai dengan benar</small>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('pegawai.show', $pegawai->id) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <form action="{{ route('pegawai.update', $pegawai->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- ================= KIRI : DATA PRIBADI ================= --}}
                <div class="col-md-6">
                    <div class="card card-outline card-info h-100">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-id-card"></i> Data Pribadi
                            </h3>
                        </div>

                        <div class="card-body">

                            <div class="form-group">
                                <label>Nama Pegawai</label>
                                <input type="text" name="nama_rekening" class="form-control"
                                    value="{{ old('nama_rekening', $pegawai->nama_rekening) }}">
                            </div>

                            <div class="form-group">
                                <label>NIK</label>
                                <input type="text" name="nik" class="form-control"
                                    value="{{ old('nik', $pegawai->nik) }}">
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="form-control"
                                        value="{{ old('tempat_lahir', $pegawai->tempat_lahir) }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control"
                                        value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Nama Ibu Kandung</label>
                                <input type="text" name="nama_ibu_kandung" class="form-control"
                                    value="{{ old('nama_ibu_kandung', $pegawai->nama_ibu_kandung) }}">
                            </div>

                            <div class="form-group">
                                <label>Pendidikan Terakhir</label>
                                <input type="text" name="pend_terakhir" class="form-control"
                                    value="{{ old('pend_terakhir', $pegawai->pend_terakhir) }}">
                            </div>

                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="alamat_gtk" class="form-control" rows="4">{{ old('alamat_gtk', $pegawai->alamat_gtk) }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ================= KANAN : DATA LAIN ================= --}}
                <div class="col-md-6">

                    {{-- DATA KEPEGAWAIAN --}}
                    <div class="card card-outline card-success mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-briefcase"></i> Data Kepegawaian
                            </h3>
                        </div>

                        <div class="card-body">
                            <div class="form-group">
                                <label>Jabatan</label>
                                <input type="text" name="jabatan" class="form-control"
                                    value="{{ old('jabatan', $pegawai->jabatan) }}">
                            </div>

                            <div class="form-group">
                                <label>PEG ID</label>
                                <input type="text" name="pegid" class="form-control"
                                    value="{{ old('pegid', $pegawai->pegid) }}">
                            </div>

                            <div class="form-group">
                                <label>Madrasah</label>
                                <select name="id_madrasah" class="form-control">
                                    @foreach ($madrasah as $m)
                                        <option value="{{ $m->id }}"
                                            {{ old('id_madrasah', $pegawai->id_madrasah) == $m->id ? 'selected' : '' }}>
                                            {{ $m->nama_madrasah }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-0">
                                <label>NPWP</label>
                                <input type="text" name="npwp" class="form-control"
                                    value="{{ old('npwp', $pegawai->npwp) }}">
                            </div>
                        </div>
                    </div>

                    {{-- DATA KONTAK --}}
                    <div class="card card-outline card-warning mb-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-phone"></i> Data Kontak
                            </h3>
                        </div>

                        <div class="card-body">
                            <div class="form-group">
                                <label>No HP</label>
                                <input type="text" name="nomor_hp" class="form-control"
                                    value="{{ old('nomor_hp', $pegawai->nomor_hp) }}">
                            </div>

                            <div class="form-group mb-0">
                                <label>Email</label>
                                <input type="email" name="alamat_email" class="form-control"
                                    value="{{ old('alamat_email', $pegawai->alamat_email) }}">
                            </div>
                        </div>
                    </div>

                    {{-- DATA BANK --}}
                    <div class="card card-outline card-danger">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-university"></i> Data Bank
                            </h3>
                        </div>

                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label>No Rekening Bank DKI</label>
                                <input type="text" name="no_rek_bank_dki" class="form-control"
                                    value="{{ old('no_rek_bank_dki', $pegawai->no_rek_bank_dki) }}">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ACTION --}}
            <div class="card mt-3">
                <div class="card-body text-right">
                    <a href="{{ route('pegawai.show', $pegawai->id) }}" class="btn btn-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection
