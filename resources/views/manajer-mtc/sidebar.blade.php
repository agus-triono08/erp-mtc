<ul class="navbar-nav bg-gradient-white sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard.manajermtc') }}">
        <div class="sidebar-brand-icon rotate-n-15"
            style="background-color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; width: 50px; height: 50px;">
            <img src="https://image1ws.indotrading.com/s3/webp/co48220/companylogo/w200-h200/sinkoprimaalloy3ecab9ce-ecdf-4b3a-b6d0-db7126ae03f4.png" 
                alt="LOGO" 
                style="width: 30px; height: 30px; object-fit: contain;">
        </div>
        <div class="sidebar-brand-text mx-3" style="text-transform: none; white-space: nowrap;">Sinko Prima Alloy</div>
    </a>



    <!-- Divider -->
    <!--<hr class="sidebar-divider my-0">-->

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard.manajermtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.manajermtc') }}">
            <i class="fas fa-fw fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>    

    <!-- Divider -->
    <!--<hr class="sidebar-divider">-->

    <!-- Heading -->
    <br><div class="sidebar-heading">
        <strong>Data Inventaris</strong>
    </div>

    <!-- Nav Item - Data -->
    <li class="nav-item {{ request()->routeIs('data.manajermtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('data.manajermtc') }}">
            <i class="fas fa-fw {{ request()->routeIs('data.manajermtc') ? 'fa-tools' : 'fa-toolbox' }}"></i>
            <span>Master Data</span>
        </a>
    </li>

    <!-- Nav Item - Data -->
    <li class="nav-item {{ request()->routeIs('layout.manajermtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('layout.manajermtc') }}">
            <i class="fas fa-fw {{ request()->routeIs('layout.manajermtc') ? 'fa-folder-open' : 'fa-folder' }}"></i>
            <span>Layout</span>
        </a>
    </li>

    <!-- Nav Item - History -->
    <li class="nav-item {{ request()->routeIs('manajermtc-riwayat') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('manajermtc-riwayat') }}">
            <i class="fas fa-fw {{ request()->routeIs('manajermtc-riwayat') ? 'fa-folder-open' : 'fa-history' }}"></i>
            <span>Riwayat</span>
        </a>
    </li>

    <!-- Heading -->
    <!--<br><div class="sidebar-heading">
        <strong>Alat</strong>
    </div>-->

    <!-- Nav Item - Error -->
    <!--<li class="nav-item {{ request()->routeIs('dataalaterror.manajermtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dataalaterror.manajermtc') }}">
            <i class="fas fa-fw {{ request()->routeIs('dataalaterror.manajermtc') ? 'fa-cogs' : 'fa-cog' }}"></i>
            <span>Error</span>
        </a>
    </li>-->

    <!-- Nav Item - Rusak -->
    <!--<li class="nav-item">
        <a class="nav-link">
            <i class="fas fa-fw fa-exclamation-triangle"></i>
            <span>Rusak</span>
        </a>
    </li>-->

    <!-- Nav Item - Musnah -->
    <!--<li class="nav-item">
        <a class="nav-link">
            <i class="fas fa-fw fa-times-circle"></i>
            <span>Musnah</span>
        </a>
    </li>-->

    <!-- Nav Item - Hilang -->
    <!--<li class="nav-item">
        <a class="nav-link">
            <i class="fas fa-fw fa-question-circle"></i>
            <span>Hilang</span>
        </a>
    </li>-->

    <!-- Heading -->
    <br><div class="sidebar-heading">
        <strong>Manajemen Inventaris</strong>
    </div>

    <!-- Nav Item - Peminjaman -->
    <li class="nav-item {{ request()->routeIs('peminjaman.manajermtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('peminjaman.manajermtc') }}">
            <i class="fas fa-fw {{ request()->routeIs('peminjaman.manajermtc') ? 'fa-hands-helping' : 'fa-handshake' }}"></i>
            <span>Permintaan/Peminjaman</span>
        </a>
    </li>

    <!-- Nav Item - Perawatan -->
    <li class="nav-item {{ request()->routeIs('perawatan.manajermtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('perawatan.manajermtc') }}">
            <i class="fas fa-fw {{ request()->routeIs('perawatan.manajermtc') ? 'fa-cogs' : 'fa-cog' }}"></i>
            <span>Perawatan</span>
        </a>
    </li>

    <!-- Divider -->
    <!--<hr class="sidebar-divider d-none d-md-block">-->

    <!-- Sidebar Toggler (Sidebar) -->
    <!--<div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>-->

</ul>