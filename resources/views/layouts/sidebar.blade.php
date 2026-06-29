@php
    $user = auth()->user();
@endphp

<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <img src="{{ asset('assets/images/kemenag/kemenag.png') }}" alt="Logo">
        <span class="brand-text">PENMAD</span>
    </div>

    {{-- Menu --}}
    <div class="sidebar-menu">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        {{-- ============================= --}}
        {{-- PENGAJUAN --}}
        {{-- ============================= --}}
        <div class="menu-title">
            Pengajuan Pegawai
        </div>

        <a href="{{ route('pengusulan-pegawai.index') }}"
            class="menu-item {{ request()->routeIs('pengusulan-pegawai.*') ? 'active' : '' }}">
            <i class="bi bi-person-plus"></i>
            <span>Pengusulan Pegawai</span>
        </a>

        <a href="{{ route('penonaktifan-pegawai.index') }}"
            class="menu-item {{ request()->routeIs('penonaktifan-pegawai.*') ? 'active' : '' }}">
            <i class="bi bi-person-dash"></i>
            <span>Penonaktifan Pegawai</span>
        </a>

        {{-- ============================= --}}
        {{-- DATA --}}
        {{-- ============================= --}}
        <div class="menu-title">
            Data UMP
        </div>

        <a href="{{ route('pegawai.index') }}" class="menu-item {{ request()->routeIs('pegawai.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            <span>Data Pegawai</span>
        </a>

        {{-- ============================= --}}
        {{-- ABSENSI --}}
        {{-- ============================= --}}
        <div class="menu-title">
            Absensi
        </div>

        <a href="{{ route('absensi.index') }}" class="menu-item {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i>
            <span>Absensi Pegawai</span>
        </a>

        {{-- ============================= --}}
        {{-- PEMBAYARAN --}}
        {{-- ============================= --}}
        <div class="menu-title">
            Pembayaran
        </div>

        <a href="{{ route('hak-pembayaran.index') }}"
            class="menu-item {{ request()->routeIs('hak-pembayaran.*') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i>
            <span>Hak Pembayaran</span>
        </a>

        <a href="{{ route('rekap-honor.index') }}"
            class="menu-item {{ request()->routeIs('rekap-honor.*') ? 'active' : '' }}">
            <i class="bi bi-cash-stack"></i>
            <span>Rekap Honor</span>
        </a>

        {{-- ============================= --}}
        {{-- SUPERADMIN --}}
        {{-- ============================= --}}
        @role('superadmin')
            <div class="menu-title">
                Management
            </div>

            <a href="{{ route('attendance-period.index') }}"
                class="menu-item {{ request()->routeIs('attendance-period.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i>
                <span>Periode Absensi</span>
            </a>

            <a href="{{ route('admin.users.index') }}"
                class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i>
                <span>Manajemen User</span>
            </a>

            <a href="#" class="menu-item">
                <i class="bi bi-building"></i>
                <span>Data Madrasah</span>
            </a>
        @endrole

        {{-- ============================= --}}
        {{-- AKUN --}}
        {{-- ============================= --}}
        <div class="menu-title">
            Akun
        </div>

        <a href="{{ route('auth.ubah_password') }}"
            class="menu-item {{ request()->routeIs('auth.ubah_password') ? 'active' : '' }}">
            <i class="bi bi-key"></i>
            <span>Ubah Password</span>
        </a>

    </div>

    {{-- Profile --}}
    <div class="sidebar-footer">

        <div class="profile-card">

            <div class="profile-avatar">

                <img src="{{ asset('assets/images/kemenag/kemenag.png') }}" alt="Avatar">

            </div>

            <div class="profile-info">

                <div class="profile-name">

                    {{ $user->madrasah->nama_madrasah ?? 'KANWIL DKI JAKARTA' }}

                </div>

                <div class="profile-role">

                    {{ $user->getRoleNames()->first() }}

                </div>

            </div>

        </div>

    </div>

</aside>
