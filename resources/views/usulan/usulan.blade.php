@extends('layouts.base')

@section('title', 'Usulan Pegawai')

@section('content')
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="fw-bold">Pengusulan Pegawai</h1>
                <small class="text-muted">Isi semua data pegawai dengan benar</small>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('pengusulan-pegawai.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <form action="{{ route('pengusulan-pegawai.store') }}" method="POST">
            @csrf

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
                                    value="{{ old('nama_simpatika', $pegawai->nama_simpatika) }}" required>
                                @error('nama_simpatika')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Nama Rekening</label>
                                <input type="text" name="nama_rekening"
                                    class="form-control @error('nama_rekening') is-invalid @enderror"
                                    value="{{ old('nama_rekening', $pegawai->nama_rekening) }}" required>
                                @error('nama_rekening')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>NIK</label>
                                <input type="text" id="nik" name="nik"
                                    class="form-control @error('nik') is-invalid @enderror"
                                    value="{{ old('nik', $pegawai->nik) }}" required>

                                <small id="nikError" class="text-danger d-none">
                                    NIK harus 16 digit angka
                                </small>

                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir"
                                        class="form-control @error('tempat_lahir') is-invalid @enderror"
                                        value="{{ old('tempat_lahir', $pegawai->tempat_lahir) }}" required>
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir"
                                        class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                        value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir) }}" required>
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Nama Ibu Kandung</label>
                                <input type="text" name="nama_ibu_kandung"
                                    class="form-control @error('nama_ibu_kandung') is-invalid @enderror"
                                    value="{{ old('nama_ibu_kandung', $pegawai->nama_ibu_kandung) }}" required>
                                @error('nama_ibu_kandung')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Agama</label>
                                <select name="agama" class="form-control @error('agama') is-invalid @enderror" required>
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

                                <select name="pend_terakhir"
                                    class="form-control @error('pend_terakhir') is-invalid @enderror">

                                    <option value="">-- Pilih Pendidikan --</option>

                                    <option value="SD"
                                        {{ old('pend_terakhir', $pegawai->pend_terakhir) == 'SD' ? 'selected' : '' }}>
                                        SD
                                    </option>

                                    <option value="SMP/MTs"
                                        {{ old('pend_terakhir', $pegawai->pend_terakhir) == 'SMP/MTs' ? 'selected' : '' }}>
                                        SMP/MTs
                                    </option>

                                    <option value="SMA/SMK/MA"
                                        {{ old('pend_terakhir', $pegawai->pend_terakhir) == 'SMA/SMK/MA' ? 'selected' : '' }}>
                                        SMA/SMK/MA
                                    </option>

                                    <option value="D2"
                                        {{ old('pend_terakhir', $pegawai->pend_terakhir) == 'D2' ? 'selected' : '' }}>
                                        D2
                                    </option>

                                    <option value="D3"
                                        {{ old('pend_terakhir', $pegawai->pend_terakhir) == 'D3' ? 'selected' : '' }}>
                                        D3
                                    </option>

                                    <option value="S1/D4"
                                        {{ old('pend_terakhir', $pegawai->pend_terakhir) == 'S1/D4' ? 'selected' : '' }}>
                                        S1/D4
                                    </option>

                                    <option value="S2"
                                        {{ old('pend_terakhir', $pegawai->pend_terakhir) == 'S2' ? 'selected' : '' }}>
                                        S2
                                    </option>

                                    <option value="S3"
                                        {{ old('pend_terakhir', $pegawai->pend_terakhir) == 'S3' ? 'selected' : '' }}>
                                        S3
                                    </option>

                                    <option value="Paket A"
                                        {{ old('pend_terakhir', $pegawai->pend_terakhir) == 'Paket A' ? 'selected' : '' }}>
                                        Paket A
                                    </option>

                                    <option value="Paket B"
                                        {{ old('pend_terakhir', $pegawai->pend_terakhir) == 'Paket B' ? 'selected' : '' }}>
                                        Paket B
                                    </option>

                                    <option value="Paket C"
                                        {{ old('pend_terakhir', $pegawai->pend_terakhir) == 'Paket C' ? 'selected' : '' }}>
                                        Paket C
                                    </option>

                                </select>

                                @error('pend_terakhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Alamat Domisili</label>
                                <textarea name="alamat_gtk" class="form-control @error('alamat_gtk') is-invalid @enderror" rows="3" required>{{ old('alamat_gtk', $pegawai->alamat_gtk) }}</textarea>
                                @error('alamat_gtk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Alamat Sesuai KTP</label>
                                <textarea name="alamat_sesuai_ktp" class="form-control @error('alamat_sesuai_ktp') is-invalid @enderror" rows="3"
                                    required>{{ old('alamat_sesuai_ktp', $pegawai->alamat_sesuai_ktp) }}</textarea>
                                @error('alamat_sesuai_ktp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Link Drive Foto KTP</label>
                                <input type="text" name="link_drive_foto_ktp"
                                    class="form-control @error('link_drive_foto_ktp') is-invalid @enderror"
                                    value="{{ old('link_drive_foto_ktp', $pegawai->link_drive_foto_ktp) }}" required>

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
                                <select name="jabatan_ump"
                                    class="form-control @error('jabatan_ump') is-invalid @enderror" required>
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
                                    class="form-control @error('jabatan_dinas') is-invalid @enderror" required>
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

                                @if ($isReadonly)
                                    {{-- Operator → readonly --}}
                                    <input type="text" class="form-control"
                                        value="{{ $madrasah?->nama_madrasah ?? 'Madrasah tidak ditemukan' }}" readonly>
                                    <input type="hidden" name="id_madrasah" value="{{ $madrasah?->id ?? '' }}">
                                @else
                                    {{-- Superadmin → pilih bebas --}}
                                    <select name="id_madrasah"
                                        class="form-control @error('id_madrasah') is-invalid @enderror" required>
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
                                @endif
                            </div>

                            <div class="form-group">
                                <label>NPSN</label>
                                <input type="number" name="npsn_tempat_tugas"
                                    class="form-control @error('npsn_tempat_tugas') is-invalid @enderror" required
                                    value="{{ old('npsn_tempat_tugas', $pegawai->npsn_tempat_tugas) }}">
                                @error('npsn_tempat_tugas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>NPWP</label>
                                <input type="text" id="npwp" inputmode="numeric" name="npwp"
                                    class="form-control @error('npwp') is-invalid @enderror"
                                    value="{{ old('npwp', $pegawai->npwp) }}">

                                <small id="npwpError" class="text-danger d-none">
                                    NPWP harus 15 atau 16 digit angka, tidak boleh mengandung ( spasi, ., -, _ )
                                </small>

                                @error('npwp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>PEG ID</label>
                                <input type="text" id="pegid" name="pegid"
                                    class="form-control @error('pegid') is-invalid @enderror"
                                    value="{{ old('pegid', $pegawai->pegid) }}">

                                <small id="pegidError" class="text-danger d-none">
                                    PEG ID harus 14 digit angka
                                </small>

                                @error('pegid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Dapodik</label>
                                <input type="text" class="form-control" value="TIDAK" readonly>
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
                                    value="{{ old('nomor_hp', $pegawai->nomor_hp) }}" required>
                                @error('nomor_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="alamat_email"
                                    class="form-control @error('alamat_email') is-invalid @enderror"
                                    value="{{ old('alamat_email', $pegawai->alamat_email) }}" required>
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
                                <input type="text" id="rek" name="no_rek_bank_dki"
                                    class="form-control @error('no_rek_bank_dki') is-invalid @enderror"
                                    value="{{ old('no_rek_bank_dki', $pegawai->no_rek_bank_dki) }}">

                                <small id="rekError" class="text-danger d-none">
                                    No rekening harus 11 digit angka
                                </small>

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

    <script>
        function validateInput(id, regex, errorId) {
            const input = document.getElementById(id);
            const error = document.getElementById(errorId);

            input.addEventListener('input', function() {
                const val = this.value;

                // hanya angka (auto bersihin kalau user ngetik huruf)
                this.value = val.replace(/\D/g, '');

                if (this.value === '') {
                    error.classList.add('d-none');
                    this.classList.remove('is-invalid');
                    return;
                }

                if (!regex.test(this.value)) {
                    error.classList.remove('d-none');
                    this.classList.add('is-invalid');
                } else {
                    error.classList.add('d-none');
                    this.classList.remove('is-invalid');
                }
            });
        }

        // panggil validasi
        validateInput('nik', /^\d{16}$/, 'nikError');
        validateInput('npwp', /^\d{15,16}$/, 'npwpError');
        validateInput('pegid', /^\d{14}$/, 'pegidError');
        validateInput('rek', /^\d{11}$/, 'rekError');
    </script>
@endsection
