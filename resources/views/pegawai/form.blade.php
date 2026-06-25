@extends('layouts.base')

@section('title', $mode === 'edit' ? 'Edit Pegawai' : 'Tambah Pegawai')

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="fw-bold">{{ $mode === 'edit' ? 'Edit Pegawai' : 'Tambah Pegawai' }}</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ $mode === 'edit' ? route('pegawai.show', $pegawai->id) : route('pegawai.index') }}"
                    class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ $mode === 'edit' ? route('pegawai.update', $pegawai->id) : route('pegawai.store') }}" method="POST">
            @csrf
            @if ($mode === 'edit')
                @method('PUT')
            @endif

            <div class="row">

                {{-- Kiri: Data Pribadi --}}
                <div class="col-md-6">
                    <div class="card card-outline card-info h-100">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-id-card"></i> Data Pribadi</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label>Nama Simpatika</label>
                                <input type="text" name="nama_simpatika"
                                    class="form-control @error('nama_simpatika') is-invalid @enderror"
                                    value="{{ old('nama_simpatika', $pegawai->nama_simpatika) }}">
                                @error('nama_simpatika')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Nama Rekening</label>
                                <input type="text" name="nama_rekening"
                                    class="form-control @error('nama_rekening') is-invalid @enderror"
                                    value="{{ old('nama_rekening', $pegawai->nama_rekening) }}">
                                @error('nama_rekening')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>NIK</label>
                                <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror"
                                    value="{{ old('nik', $pegawai->nik) }}">
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir"
                                        class="form-control @error('tempat_lahir') is-invalid @enderror"
                                        value="{{ old('tempat_lahir', $pegawai->tempat_lahir) }}">
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir"
                                        class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                        value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir) }}">
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Nama Ibu Kandung</label>
                                <input type="text" name="nama_ibu_kandung"
                                    class="form-control @error('nama_ibu_kandung') is-invalid @enderror"
                                    value="{{ old('nama_ibu_kandung', $pegawai->nama_ibu_kandung) }}">
                                @error('nama_ibu_kandung')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Agama</label>
                                <select name="agama" class="form-control @error('agama') is-invalid @enderror">
                                    <option value="">-- Pilih Agama --</option>
                                    @foreach (['ISLAM', 'KRISTEN', 'HINDU', 'BUDHA', 'KONGHUCU'] as $agama)
                                        <option value="{{ $agama }}"
                                            {{ old('agama', $pegawai->agama) == $agama ? 'selected' : '' }}>
                                            {{ $agama }}</option>
                                    @endforeach
                                </select>
                                @error('agama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Pendidikan Terakhir</label>
                                <input type="text" name="pend_terakhir"
                                    class="form-control @error('pend_terakhir') is-invalid @enderror"
                                    value="{{ old('pend_terakhir', $pegawai->pend_terakhir) }}">
                                @error('pend_terakhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Alamat Domisili</label>
                                <textarea name="alamat_gtk" class="form-control @error('alamat_gtk') is-invalid @enderror" rows="3">{{ old('alamat_gtk', $pegawai->alamat_gtk) }}</textarea>
                                @error('alamat_gtk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Alamat Sesuai KTP</label>
                                <textarea name="alamat_sesuai_ktp" class="form-control @error('alamat_sesuai_ktp') is-invalid @enderror" rows="3">{{ old('alamat_sesuai_ktp', $pegawai->alamat_sesuai_ktp) }}</textarea>
                                @error('alamat_sesuai_ktp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Link Drive Foto KTP</label>
                                <input type="text" name="link_drive_foto_ktp"
                                    class="form-control @error('link_drive_foto_ktp') is-invalid @enderror"
                                    value="{{ old('link_drive_foto_ktp', $pegawai->link_drive_foto_ktp) }}">

                                <small class="form-text text-muted">
                                    Mohon masukkan <strong>link langsung ke file foto KTP</strong> yang telah dibagikan
                                    (share) di Google Drive.
                                    Pastikan link mengarah langsung ke dokumen/foto KTP yang dimaksud, <strong>bukan ke
                                        folder Drive</strong>,
                                    serta dapat diakses oleh pihak yang berwenang untuk keperluan verifikasi data.
                                </small>

                                @error('link_drive_foto_ktp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Data Kepegawaian, Kontak, Bank --}}
                <div class="col-md-6">
                    {{-- Data Kepegawaian --}}
                    <div class="card card-outline card-success mb-3">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-briefcase"></i> Data Kepegawaian</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label>Jabatan UMP</label>
                                <select name="jabatan_ump" class="form-control @error('jabatan_ump') is-invalid @enderror">
                                    <option value="">-- Pilih Jabatan UMP --</option>
                                    @foreach ($jabatanUMPList as $jabatan)
                                        <option value="{{ $jabatan }}"
                                            {{ old('jabatan_ump', $pegawai->jabatan_ump) == $jabatan ? 'selected' : '' }}>
                                            {{ $jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jabatan_ump')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Jabatan Dinas</label>
                                <select name="jabatan_dinas"
                                    class="form-control @error('jabatan_dinas') is-invalid @enderror">
                                    <option value="">-- Pilih Jabatan Dinas --</option>
                                    @foreach (['PENDIDIK', 'TENDIK'] as $jabatan)
                                        <option value="{{ $jabatan }}"
                                            {{ old('jabatan_dinas', $pegawai->jabatan_dinas) == $jabatan ? 'selected' : '' }}>
                                            {{ $jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jabatan_dinas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <input type="hidden" name="status_asn" value="{{ old('status_asn', 'NON ASN') }}">

                            <input type="hidden" name="status_pegawai" value="{{ old('status_pegawai', 'AKTIF') }}">

                            <div class="form-group">
                                <label>Madrasah</label>
                                <select name="id_madrasah"
                                    class="form-control @error('id_madrasah') is-invalid @enderror">
                                    <option value="">-- Pilih Madrasah --</option>
                                    @foreach ($madrasah as $m)
                                        <option value="{{ $m->id }}"
                                            {{ old('id_madrasah', $pegawai->id_madrasah) == $m->id ? 'selected' : '' }}>
                                            {{ $m->nama_madrasah }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_madrasah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>NPSN</label>
                                <input type="text" name="npsn_tempat_tugas"
                                    class="form-control @error('npsn_tempat_tugas') is-invalid @enderror"
                                    value="{{ old('npsn_tempat_tugas', $pegawai->npsn_tempat_tugas) }}">
                                @error('npsn_tempat_tugas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>NPWP</label>
                                <input type="text" name="npwp"
                                    class="form-control @error('npwp') is-invalid @enderror"
                                    value="{{ old('npwp', $pegawai->npwp) }}">
                                @error('npwp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>PEG ID</label>
                                <input type="text" name="pegid"
                                    class="form-control @error('pegid') is-invalid @enderror"
                                    value="{{ old('pegid', $pegawai->pegid) }}">
                                @error('pegid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Dapodik</label>
                                <select name="dapodik" class="form-control @error('dapodik') is-invalid @enderror">
                                    <option value="">-- Pilih Dapodik --</option>
                                    @foreach (['YA', 'TIDAK'] as $dapodik)
                                        <option value="{{ $dapodik }}"
                                            {{ old('dapodik', $pegawai->dapodik) == $dapodik ? 'selected' : '' }}>
                                            {{ $dapodik }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('dapodik')
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
                                    value="{{ old('nomor_hp', $pegawai->nomor_hp) }}">
                                @error('nomor_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="alamat_email"
                                    class="form-control @error('alamat_email') is-invalid @enderror"
                                    value="{{ old('alamat_email', $pegawai->alamat_email) }}">
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
                            <div class="form-group">
                                <label>No Rekening Bank DKI</label>
                                <input type="text" name="no_rek_bank_dki"
                                    class="form-control @error('no_rek_bank_dki') is-invalid @enderror"
                                    value="{{ old('no_rek_bank_dki', $pegawai->no_rek_bank_dki) }}">
                                @error('no_rek_bank_dki')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            @if ($mode === 'edit')
                {{-- Validasi Data --}}
                <div class="card card-outline card-primary mt-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-check-circle"></i> Validasi Data
                        </h3>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label>Apakah NIK sudah sesuai ?</label>
                            <select name="nik_sesuai" class="form-control @error('nik_sesuai') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="YA"
                                    {{ old('nik_sesuai', $pegawai->nik_sesuai) == 'YA' ? 'selected' : '' }}>
                                    YA
                                </option>
                                <option value="TIDAK"
                                    {{ old('nik_sesuai', $pegawai->nik_sesuai) == 'TIDAK' ? 'selected' : '' }}>
                                    TIDAK
                                </option>
                            </select>
                            @error('nik_sesuai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Apakah NIK sudah terdaftar di EMIS 4.0?</label>
                            <select name="nik_terdaftar_emis40"
                                class="form-control @error('nik_terdaftar_emis40') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="YA"
                                    {{ old('nik_terdaftar_emis40', $pegawai->nik_terdaftar_emis40) == 'YA' ? 'selected' : '' }}>
                                    YA
                                </option>
                                <option value="TIDAK"
                                    {{ old('nik_terdaftar_emis40', $pegawai->nik_terdaftar_emis40) == 'TIDAK' ? 'selected' : '' }}>
                                    TIDAK
                                </option>
                            </select>
                            @error('nik_terdaftar_emis40')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">

                            <label class="font-weight-bold">
                                Link Drive Bukti EMIS 4.0
                            </label>

                            <input type="text" name="link_drive_emis40"
                                class="form-control @error('link_drive_emis40') is-invalid @enderror"
                                value="{{ old('link_drive_emis40', $pegawai->link_drive_emis40) }}"
                                placeholder="https://drive.google.com/...">

                            @error('link_drive_emis40')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror


                            {{-- INFO BOX --}}
                            <div class="mt-2 p-2 border rounded bg-light">

                                <small class="text-muted">
                                    <i class="fas fa-info-circle text-primary"></i>
                                    Pastikan link yang dimasukkan adalah <b>file langsung</b>, bukan folder Google Drive.
                                </small>

                                <br>

                                <small class="text-muted">
                                    Link harus dapat diakses untuk proses verifikasi data.
                                </small>

                                {{-- LINK CONTOH --}}
                                <div class="mt-2">
                                    <a href="#" class="text-primary font-weight-bold" data-toggle="modal"
                                        data-target="#modalEmis40">
                                        <i class="fas fa-image"></i>
                                        Lihat Contoh Bukti EMIS 4.0
                                    </a>
                                </div>

                            </div>

                        </div>

                        <div class="form-group">
                            <label>Apakah NIK sudah terdaftar di EMIS GTK?</label>
                            <select name="nik_terdaftar_emis_gtk"
                                class="form-control @error('nik_terdaftar_emis_gtk') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="YA"
                                    {{ old('nik_terdaftar_emis_gtk', $pegawai->nik_terdaftar_emis_gtk) == 'YA' ? 'selected' : '' }}>
                                    YA
                                </option>
                                <option value="TIDAK"
                                    {{ old('nik_terdaftar_emis_gtk', $pegawai->nik_terdaftar_emis_gtk) == 'TIDAK' ? 'selected' : '' }}>
                                    TIDAK
                                </option>
                            </select>
                            @error('nik_terdaftar_emis_gtk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">

                            <label class="font-weight-bold">
                                Link Drive Bukti EMIS GTK
                            </label>

                            <input type="text" name="link_drive_emis_gtk"
                                class="form-control @error('link_drive_emis_gtk') is-invalid @enderror"
                                value="{{ old('link_drive_emis_gtk', $pegawai->link_drive_emis_gtk) }}"
                                placeholder="https://drive.google.com/...">

                            @error('link_drive_emis_gtk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- INFO BOX --}}
                            <div class="mt-2 p-2 border rounded bg-light">

                                <small class="text-muted">
                                    <i class="fas fa-info-circle text-primary"></i>
                                    Pastikan link yang dimasukkan adalah <b>file langsung</b>, bukan folder Google Drive.
                                </small>
                                <br>
                                <small class="text-muted">
                                    Link harus dapat diakses untuk proses verifikasi data EMIS GTK.
                                </small>

                                {{-- LINK CONTOH --}}
                                <div class="mt-2">
                                    <a href="#" class="text-primary font-weight-bold" data-toggle="modal"
                                        data-target="#modalEmisGTK">
                                        <i class="fas fa-image"></i>
                                        Lihat Contoh Bukti EMIS GTK
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endif

            {{-- Action Button --}}
            <div class="card mt-3">
                <div class="card-body text-right">
                    <a href="{{ $mode === 'edit' ? route('pegawai.show', $pegawai->id) : route('pegawai.index') }}"
                        class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn {{ $mode === 'edit' ? 'btn-warning' : 'btn-success' }}">
                        <i class="fas fa-save"></i> {{ $mode === 'edit' ? 'Simpan Perubahan' : 'Simpan' }}
                    </button>
                </div>
            </div>

        </form>

    </div>


    <div class="modal fade" id="modalEmis40" tabindex="-1" role="dialog" aria-labelledby="modalEmis40Label"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalEmis40Label">
                        Contoh Bukti EMIS 4.0
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <img src="{{ asset('assets/images/emis-40.png') }}" class="img-fluid" style="max-height: 75vh;"
                        alt="Contoh Bukti EMIS 4.0">
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEmisGTK" tabindex="-1" role="dialog" aria-labelledby="modalEmisGTKLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalEmisGTKLabel">
                        Contoh Bukti EMIS GTK
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <img src="{{ asset('assets/images/emis-gtk.png') }}" class="img-fluid" style="max-height: 75vh;"
                        alt="Contoh Bukti EMIS 4.0">
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
