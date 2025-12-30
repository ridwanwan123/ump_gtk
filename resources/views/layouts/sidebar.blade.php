<div class="sidebar">
    {{-- User Panel --}}
    <div class="user-panel d-flex align-items-center mt-3 mb-3">
        <div class="image">
            <img src="{{ asset('assets/images/kemenag/kemenag.png') }}" alt="User Image" class="user-image">
        </div>
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
                    class="nav-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-calendar-check"></i>
                    <p>Absensi Pegawai</p>
                </a>
            </li>

            {{-- ADMIN ONLY --}}
            @role('superadmin')
                <li class="nav-header">Admin</li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}"
                        class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Manajemen User</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.madrasah.index') }}"
                        class="nav-link {{ request()->routeIs('admin.madrasah.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-school"></i>
                        <p>Data Madrasah</p>
                    </a>
                </li>
            @endrole

        </ul>
    </nav>
</div>
