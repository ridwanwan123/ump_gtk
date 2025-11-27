@extends('layouts.base')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <h1>
                Selamat Datang, {{ auth()->user()->name }}!

                Unit Kerja: {{ auth()->user()->unit_kerja }}
            </h1>
        </div>
    </div>
@endsection

@push('scripts')

@endpush