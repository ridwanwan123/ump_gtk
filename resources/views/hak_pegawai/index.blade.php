@extends('layouts.base')

@section('title', 'Hak Pembayaran Pegawai')

@section('content')

    <div class="container">

        {{-- ========================= --}}
        {{-- FILTER --}}
        {{-- ========================= --}}
        <div class="card mb-3">
            <div class="card-body">

                <form method="GET" class="row g-2">

                    <div class="col-md-4">
                        <select name="madrasah" class="form-control">
                            <option value="">-- Madrasah --</option>
                            @foreach ($madrasahList as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_madrasah }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="tw" class="form-control">
                            <option value="1">TW 1</option>
                            <option value="2">TW 2</option>
                            <option value="3">TW 3</option>
                            <option value="4">TW 4</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}">
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>

                </form>

            </div>
        </div>

        {{-- ========================= --}}
        {{-- ACTION BUTTON --}}
        {{-- ========================= --}}
        <div class="mb-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalHak">
                + Input Hak Pembayaran
            </button>
        </div>

        {{-- ========================= --}}
        {{-- TABLE --}}
        {{-- ========================= --}}
        <div class="card">
            <div class="card-body table-responsive">

                <table class="table table-bordered text-center">

                    <thead>
                        <tr>
                            <th>Nama</th>

                            @foreach ($bulan as $b)
                                <th>{{ \Carbon\Carbon::create()->month($b)->format('M') }}</th>
                            @endforeach

                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($pegawai as $p)
                            <tr>
                                <td class="text-start">{{ $p->nama_rekening }}</td>

                                @foreach ($bulan as $b)
                                    @php
                                        $status = $hakMap[$p->id][$b] ?? 0;
                                    @endphp

                                    <td>
                                        @if ((int) $status === 1)
                                            <span class="text-success fw-bold">✔</span>
                                        @else
                                            <span class="text-danger fw-bold">✘</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#modalEdit{{ $p->id }}">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>
        </div>

    </div>

    {{-- ========================= --}}
    {{-- MODAL INPUT --}}
    {{-- ========================= --}}
    @include('hak_pegawai.modal-create')

@endsection
