<div class="sidebar">
    <!-- Sidebar user (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="{{ asset('assets/images/kemenag/kemenag.png') }}" alt="User Image">
        </div>
        <div class="info">
            <a href="#" class="d-block">{{ auth()->user()->unit_kerja }} Jakarta</a>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
                <a href="../widgets.html" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Dashboard
                    </p>
                </a>
            </li>
            <li class="nav-header">Menu</li>
            <li class="nav-item">
                <a href="simple.html" class="nav-link active">
                    <i class="nav-icon fas fa-table"></i>
                    <p>Simple Tables</p>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
