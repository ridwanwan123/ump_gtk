@extends('layouts.base')

@section('title', 'Manajemen User')

@push('styles')
    <style>
        .user-card {
            border-radius: 12px;
            transition: all .3s ease;
        }

        .user-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, .08);
        }

        .table td,
        .table th {
            vertical-align: middle;
            white-space: nowrap;
        }

        .badge-role {
            font-size: 0.75rem;
            padding: 0.35em 0.6em;
        }

        .btn-action {
            padding: 0.25rem 0.45rem;
        }

        .card-footer {
            overflow: visible !important;
        }

        .pagination {
            margin: 0;
            justify-content: center;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- PAGE HEADER --}}
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="m-0 text-bold">
                    <i class="fas fa-user-cog mr-1 text-info"></i>
                    Manajemen User
                </h1>
                <small class="text-muted">Kelola akun pengguna aplikasi</small>
            </div>
            <div class="col-sm-6 text-right">
                <a href="" class="btn btn-info btn-sm" hidden>
                    <i class="fas fa-user-plus mr-1"></i> Tambah User
                </a>
            </div>
        </div>

        {{-- STAT BOX --}}
        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info user-card">
                    <div class="inner">
                        <h3>{{ $totalUsers }}</h3>
                        <p>Total User</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <div class="small-box bg-danger user-card">
                    <div class="inner">
                        <h3>{{ $totalSuperadmin }}</h3>
                        <p>Superadmin</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning user-card">
                    <div class="inner">
                        <h3>{{ $totalOperator }}</h3>
                        <p>Operator</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>



        {{-- TABLE USER --}}
        <div class="card card-outline card-info shadow-sm mt-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list mr-1"></i> Daftar User</h3>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $i => $user)
                            @php
                                $role = $user->roles->first()?->name ?? '-';
                                $roleClass = match ($role) {
                                    'superadmin' => 'badge-danger',
                                    'admin' => 'badge-warning',
                                    'pegawai' => 'badge-info',
                                    default => 'badge-secondary',
                                };
                            @endphp
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $roleClass }} badge-role">{{ ucfirst($role) }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($user->status ?? true)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning btn-action" data-toggle="modal"
                                        data-target="#modalRole" data-id="{{ $user->id }}"
                                        data-nama="{{ $user->name }}" data-role="{{ $role }}">
                                        <i class="fas fa-user-shield"></i> Atur Role
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
            </div>

        </div>

    </div>

    {{-- MODAL ATUR ROLE --}}
    <div class="modal fade" id="modalRole" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form id="formRole" method="POST">
                    @csrf
                    <div class="modal-header bg-info">
                        <h5 class="modal-title"><i class="fas fa-user-shield mr-1"></i> Atur Role User</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="userId" name="user_id">
                        <div class="form-group">
                            <label>Nama User</label>
                            <input type="text" class="form-control" id="namaUser" readonly>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" id="roleUser" name="role">
                                @foreach ($roles as $r)
                                    <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info btn-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Role
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @if(session('swal_success'))
        <script>
            Swal.fire({
                title: '✅ Berhasil!',
                text: "{{ session('swal_success') }}",
                icon: 'success',
                iconColor: '#28a745',
                color: '#ffffff',
                showConfirmButton: true,
                confirmButtonText: 'Oke',
                confirmButtonColor: '#ffc107',
                timer: 2000,
                timerProgressBar: true
            });
        </script>
    @endif

    <script>
        $('#modalRole').on('show.bs.modal', function(event) {
            let button = $(event.relatedTarget);
            let id = button.data('id');
            let nama = button.data('nama');
            let role = button.data('role');

            $('#userId').val(id);
            $('#namaUser').val(nama);
            $('#roleUser').val(role);

            // Set form action
            $('#formRole').attr('action', '/admin/users/' + id + '/role');
        });
    </script>
@endpush
