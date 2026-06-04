<div class="sidebar">
    {{-- User Panel --}}
    <div class="user-panel d-flex align-items-center mt-3 mb-3">
        {{-- <div class="image"> --}}
        <img src="{{ asset('assets/images/kemenag/kemenag.png') }}" alt="User Image" class="user-image">
        {{-- </div> --}}
        <div class="info ml-2">
            <a href="#" class="d-block">
                {{ auth()->user()->madrasah->nama_madrasah ?? 'KANWIL DKI JAKARTA' }}
            </a>
            <small>{{ auth()->user()->getRoleNames()->first() }}</small>
        </div>
    </div>

    {{-- Menu --}}
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            {{-- DASHBOARD --}}
            <li class="nav-item">
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            {{-- PENGAJUAN PEGAWAI --}}
            <li class="nav-header">Pengajuan Pegawai</li>

            <li class="nav-item">
                <a href="{{ route('pengusulan-pegawai.index') }}"
                    class="nav-link {{ request()->routeIs('pengusulan-pegawai.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-plus"></i>
                    <p>Pengusulan Pegawai</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('penonaktifan-pegawai.index') }}"
                    class="nav-link {{ request()->routeIs('penonaktifan-pegawai.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-slash"></i>
                    <p>Penonaktifan Pegawai</p>
                </a>
            </li>

            {{-- DATA UMP --}}
            <li class="nav-header">Data UMP</li>
            <li class="nav-item">
                <a href="{{ route('pegawai.index') }}"
                    class="nav-link {{ request()->routeIs('pegawai.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Data Pegawai</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('absensi.index') }}"
                    class="nav-link {{ request()->routeIs('absensi.index', 'absensi.create', 'absensi.edit') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-calendar-check"></i>
                    <p>Absensi Pegawai</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('hak-pembayaran.index') }}"
                    class="nav-link {{ request()->routeIs('hak-pembayaran.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-calendar-check"></i>
                    <p>Hak Pembayaran</p>
                </a>
            </li>

            {{-- ADMIN ONLY --}}
            @role('superadmin')
                <li class="nav-header">Absensi</li>
                <li class="nav-item">
                    <a href="{{ route('attendance-period.index') }}"
                        class="nav-link {{ request()->routeIs('attendance-period.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            Periode Absensi
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('rekap-honor.index') }}"
                        class="nav-link {{ request()->routeIs('rekap-honor.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Rekap Honor</p>
                    </a>
                </li>

                <li class="nav-header">Management</li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}"
                        class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Manajemen User</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link {{ request()->routeIs('admin.madrasah.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-school"></i>
                        <p>Data Madrasah</p>
                    </a>
                </li>
            @endrole

            {{-- PENGATURAN AKUN --}}
            <li class="nav-header">Akun</li>
            <li class="nav-item">
                <a href="{{ route('auth.ubah_password') }}"
                    class="nav-link {{ request()->routeIs('auth.ubah_password') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-key"></i>
                    <p>Ubah Password</p>
                </a>
            </li>

        </ul>
    </nav>
</div>
