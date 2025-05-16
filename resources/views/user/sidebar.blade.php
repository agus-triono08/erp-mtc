<ul class="navbar-nav bg-gradient-white sidebar sidebar-dark accordion" id="accordionSidebar"
style="position: sticky; height: 100vh; top: 0;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard.user') }}">
        <div class="sidebar-brand-icon"
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
    <li class="nav-item {{ request()->routeIs('dashboard.user') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.user') }}">
            <i class="fas fa-fw fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>    

    <!-- Divider -->
    <!--<hr class="sidebar-divider">-->

    <!-- Heading -->
    <br><div class="sidebar-heading">
        <strong>Alat/Mesin</strong>
    </div>

    <!-- Nav Item - Data -->
    <li class="nav-item {{ request()->routeIs('data.user') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('data.user') }}">
            <i class="fas fa-fw {{ request()->routeIs('data.user') ? 'fa-tools' : 'fa-toolbox' }}"></i>
            <span>Alat/Mesin</span>
        </a>
    </li>

    <!-- Nav Item - History -->
    <!-- <li class="nav-item {{ request()->routeIs('adminmtc-riwayat') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('adminmtc-riwayat') }}">
            <i class="fas fa-fw {{ request()->routeIs('adminmtc-riwayat') ? 'fa-folder-open' : 'fa-history' }}"></i>
            <span>Riwayat</span>
        </a>
    </li> -->

    <!-- Heading -->
    <!--<br><div class="sidebar-heading">
        <strong>Alat</strong>
    </div>-->

    <!-- Nav Item - Error -->
    <!--<li class="nav-item {{ request()->routeIs('dataalaterror.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dataalaterror.adminmtc') }}">
            <i class="fas fa-fw {{ request()->routeIs('dataalaterror.adminmtc') ? 'fa-cogs' : 'fa-cog' }}"></i>
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
        <strong>Transfer Data</strong>
    </div>

    <!-- Nav Item - Peminjaman -->
    <li class="nav-item {{ request()->routeIs('peminjaman.user') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('peminjaman.user') }}">
            <i class="fas fa-fw {{ request()->routeIs('peminjaman.user') ? 'fa-hands-helping' : 'fa-handshake' }}"></i>
            <span>Permintaan/Peminjaman</span>
        </a>
    </li>

    <!-- Heading -->
    <br><div class="sidebar-heading">
        <strong>Penggantian</strong>
    </div>

    {{-- Nav Item - Data Hilang --}}
    <li class="nav-item {{ request()->routeIs('datahilang.user') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('datahilang.user') }}">
            <i class="bi {{ request()->routeIs('datahilang.user') ? 'bi-repeat' : 'bi-arrow-repeat' }}"></i>
            <span>Penggantian Alat/Mesin</span>
        </a>
    </li>

    <!-- Nav Item - Perawatan -->
    {{-- <li class="nav-item {{ request()->routeIs('perawatan.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('perawatan.adminmtc') }}">
            <i class="fas fa-fw {{ request()->routeIs('perawatan.adminmtc') ? 'fa-cogs' : 'fa-cog' }}"></i>
            <span>Perawatan</span>
        </a>
    </li> --}}

    <!-- Divider -->
    <!--<hr class="sidebar-divider d-none d-md-block">-->

    <!-- Sidebar Toggler (Sidebar) -->
    <!--<div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>-->

</ul>