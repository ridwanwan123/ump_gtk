<div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="{{ asset('assets/images/kemenag/kemenag.png') }}" alt="User Image">
        </div>
        <div class="info">
            <a href="#" class="d-block">{{ auth()->user()->madrasah->nama_madrasah ?? 'KANWIL DKI JAKARTA' }}</a>
        </div>
    </div>

    <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{ route(auth()->user()->hasRole('superadmin') 
                    ? 'admin.dashboard' 
                    : 'bendahara.dashboard') }}"
            class="nav-link {{ request()->routeIs('admin.dashboard', 'bendahara.dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <li class="nav-header">Data Pegawai</li>
        <li class="nav-item">
            <a href="{{ route('admin.pegawai.index') }}" class="nav-link {{ request()->routeIs('admin.pegawai.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>Data Pegawai</p>
            </a>
        </li>
    </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>