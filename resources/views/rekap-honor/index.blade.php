@extends('layouts.base')

@section('title', 'Rekap Honor Pegawai')

@push('styles')
<style>
    .card-filter {
        border-radius: 0.6rem;
        box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    }

    .info-box-custom {
        background: #f8fafc;
        border-left: 5px solid #0d6efd;
        border-radius: 8px;
        padding: 12px 15px;
    }

    .info-box-custom ul {
        padding-left: 18px;
        margin-bottom: 0;
    }

    .table-responsive {
        overflow-x: auto;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ALERT PENJELASAN --}}
    <div class="alert alert-info shadow-sm">
        <h5 class="mb-2"><i class="fas fa-info-circle"></i> Informasi Rekap Honor</h5>
        <ul class="mb-0 small">
            <li>Pilih <b>bulan</b> yang ingin dihitung (bisa lebih dari satu).</li>
            <li><b>Banyak bulan</b> akan dihitung otomatis dari jumlah bulan yang dipilih.</li>
            <li>Honor dihitung per bulan dan akan dikalikan jumlah bulan.</li>
            <li>Potongan ketidakhadiran:</li>
            <ul>
                <li>Sakit (S) = 0%</li>
                <li>Izin (I) = 2.5%</li>
                <li>Tanpa Keterangan (TK) = 5%</li>
            </ul>
            <li>Hasil akan menampilkan total kotor, potongan, dan jumlah bersih.</li>
        </ul>
    </div>

    {{-- FILTER --}}
    <div class="card card-primary card-outline mb-3 card-filter">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Filter Rekap Honor</h3>
        </div>

        <div class="card-body">
            <form method="GET" action="">

                <div class="row">

                    {{-- Tahun --}}
                    <div class="col-md-3">
                        <label>Tahun</label>
                        <input type="number" name="tahun" class="form-control"
                            value="{{ request('tahun', date('Y')) }}">
                    </div>

                    {{-- Bulan --}}
                    <div class="col-md-5">
                        <label>Pilih Bulan</label>
                        <select name="bulan[]" class="form-control select2" multiple required>
                            @foreach ([
                                1=>'Januari',2=>'Februari',3=>'Maret',
                                4=>'April',5=>'Mei',6=>'Juni',
                                7=>'Juli',8=>'Agustus',9=>'September',
                                10=>'Oktober',11=>'November',12=>'Desember'
                            ] as $key => $val)
                                <option value="{{ $key }}"
                                    {{ collect(request('bulan'))->contains($key) ? 'selected' : '' }}>
                                    {{ $val }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Bisa pilih lebih dari satu bulan</small>
                    </div>

                    {{-- Honor --}}
                    <div class="col-md-4">
                        <label>Honor per Bulan (Rp)</label>
                        <input type="number" name="honor" class="form-control"
                            placeholder="Contoh: 3900000"
                            value="{{ request('honor') }}" required>
                    </div>

                </div>

                <hr>

                <div class="row">

                    {{-- Madrasah --}}
                    <div class="col-md-4">
                        <label>Madrasah</label>
                        <select name="madrasah" class="form-control">
                            <option value="">-- Semua Madrasah --</option>
                            @foreach ($madrasahs as $m)
                                <option value="{{ $m->id }}"
                                    {{ request('madrasah') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama_madrasah }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Jabatan --}}
                    <div class="col-md-4">
                        <label>Jabatan</label>
                        <select name="jabatan_ump" class="form-control">
                            <option value="">-- Semua Jabatan --</option>
                            @foreach ($jabatanList as $j)
                                <option value="{{ $j }}"
                                    {{ request('jabatan_ump') == $j ? 'selected' : '' }}>
                                    {{ $j }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Button --}}
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Tampilkan Rekap
                        </button>
                    </div>

                </div>

            </form>
        </div>
    </div>

    <a href="{{ route('rekap-honor.export', request()->all()) }}"
   class="btn btn-success">
   Export Excel
</a>
    {{-- TEMPAT HASIL (NANTI) --}}
   @if(!empty($data))

    <div class="card shadow-sm border-0 mt-3">
        <div class="card-header bg-primary text-white">
            <b>Rekap Honor Pegawai</b>
        </div>

        <div class="card-body table-responsive p-0">

            <table class="table table-bordered table-sm text-nowrap align-middle">
                <thead class="text-center bg-light">

                    {{-- HEADER LEVEL 1 --}}
                    <tr>
                        <th rowspan="2">NAMA</th>

                        <th colspan="6">JUMLAH KETIDAKHADIRAN</th>

                        <th rowspan="2">BANYAK BULAN</th>
                        <th rowspan="2">% KEHADIRAN</th>
                        <th rowspan="2">HONOR / BULAN</th>
                        <th rowspan="2">JUMLAH KOTOR</th>

                        <th colspan="3">% & POTONGAN</th>

                        <th rowspan="2">TOTAL POTONGAN</th>
                        <th rowspan="2">SETELAH POTONGAN</th>
                        <th rowspan="2">PPH</th>
                        <th rowspan="2">BERSIH</th>
                    </tr>

                    {{-- HEADER LEVEL 2 --}}
                    <tr>
                        <th>S</th>
                        <th>I</th>
                        <th>TK</th>
                        <th>DL</th>
                        <th>C</th>
                        <th>JML</th>

                        <th>0%</th>
                        <th>2.5%</th>
                        <th>5%</th>
                    </tr>

                </thead>

                <tbody>
                    @foreach($data as $row)
                        <tr class="text-center">

                            {{-- IDENTITAS --}}
                            <td class="text-start">{{ $row['nama'] }}</td>

                            {{-- ABSENSI --}}
                            <td>{{ $row['total_s'] }}</td>
                            <td>{{ $row['total_i'] }}</td>
                            <td>{{ $row['total_tk'] }}</td>
                            <td>{{ $row['total_dl'] }}</td>
                            <td>{{ $row['total_c'] }}</td>
                            <td>
                                {{ $row['total_s'] + $row['total_i'] + $row['total_tk'] + $row['total_dl'] + $row['total_c'] }}
                            </td>

                            {{-- BASIC --}}
                            <td>{{ $row['banyak_bulan'] }}</td>
                            <td>{{ $row['persen_kehadiran'] }}%</td>
                            <td>{{ number_format($row['jumlah_honor_per_bulan'], 0, ',', '.') }}</td>
                            <td>{{ number_format($row['jumlah_kotor'], 0, ',', '.') }}</td>

                            {{-- POTONGAN --}}
                            <td>0</td>
                            <td>{{ number_format($row['potongan_i'], 0, ',', '.') }}</td>
                            <td>{{ number_format($row['potongan_tk'], 0, ',', '.') }}</td>

                            {{-- FINAL --}}
                            <td>{{ number_format($row['total_potongan'], 0, ',', '.') }}</td>
                            <td>{{ number_format($row['setelah_potongan'], 0, ',', '.') }}</td>
                            <td>{{ number_format($row['pph'], 0, ',', '.') }}</td>
                            <td><b>{{ number_format($row['jumlah_bersih'], 0, ',', '.') }}</b></td>

                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>

    @endif

</div>
@endsection