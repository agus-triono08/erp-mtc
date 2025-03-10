<ul class="navbar-nav bg-gradient-white sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard.adminmtc') }}">
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
    <li class="nav-item {{ request()->routeIs('dashboard.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.adminmtc') }}">
            <i class="fas fa-fw fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>    

    <!-- Divider -->
    <!--<hr class="sidebar-divider">-->

    <!-- Heading -->
    <br><div class="sidebar-heading">
        <strong>Alat & Mesin</strong>
    </div>

    <!-- Nav Item - Data -->
    <li class="nav-item {{ request()->routeIs('data.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('data.adminmtc') }}">
            <i class="fas fa-fw {{ request()->routeIs('data.adminmtc') ? 'fa-tools' : 'fa-toolbox' }}"></i>
            <span>Alat & Mesin</span>
        </a>
    </li>

    <!-- Nav Item - Data -->
    <li class="nav-item {{ request()->routeIs('layout.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('layout.adminmtc') }}">
            <i class="fas fa-fw {{ request()->routeIs('layout.adminmtc') ? 'fa-folder-open' : 'fa-folder' }}"></i>
            <span>Layout</span>
        </a>
    </li>

    {{-- Nav Item - Data Hilang --}}
    <li class="nav-item {{ request()->routeIs('datahilang.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('datahilang.adminmtc') }}">
            <i class="fas fa-fw {{ request()->routeIs('datahilang.adminmtc') ? 'fa-search-minus' : 'fa-minus-circle' }}"></i>
            <span>Data Hilang</span>
        </a>
    </li>

    <!-- Nav Item - History -->
    <li class="nav-item {{ request()->routeIs('adminmtc-riwayat') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('adminmtc-riwayat') }}">
            <i class="fas fa-fw {{ request()->routeIs('adminmtc-riwayat') ? 'fa-folder-open' : 'fa-history' }}"></i>
            <span>Riwayat</span>
        </a>
    </li>

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
        <strong>Permintaan & Peminjaman</strong>
    </div>

    <!-- Nav Item - Peminjaman -->
    {{-- <li class="nav-item">
    <a class="nav-link" href="#" data-toggle="collapse" data-target="#kondisi-collapse" aria-expanded="true" aria-controls="kondisi-collapse">
        <i class="fas fa-fw fa-info-circle"></i>
        <span>Kondisi</span>
    </a>
    <div id="kondisi-collapse" class="collapse" aria-labelledby="kondisi-collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Kondisi</h6>
            <a class="collapse-item {{ Request::is('kondisi-error') ? 'active' : '' }}" href="{{route('kondisi-error')}}"><i class="fas fa-fw fa-bug" style="color: #169ea8;"></i> Error</a>
            <a class="collapse-item {{ Request::is('kondisi-rusak') ? 'active' : '' }}" href="{{route('kondisi-rusak')}}"><i class="fas fa-fw fa-exclamation-triangle" style="color: #169ea8;"></i> Rusak</a>
            <a class="collapse-item {{ Request::is('kondisi-musnah') ? 'active' : '' }}" href="{{route('kondisi-musnah')}}"><i class="fas fa-fw fa-skull-crossbones" style="color: #169ea8;"></i> Musnah</a>
        </div>
    </div>
    </li> --}}

    <!-- Nav Item - Peminjaman -->
    <li class="nav-item {{ request()->routeIs('peminjaman.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('peminjaman.adminmtc') }}">
            <i class="fas fa-fw {{ request()->routeIs('peminjaman.adminmtc') ? 'fa-hands-helping' : 'fa-handshake' }}"></i>
            <span>Permintaan/Peminjaman</span>
        </a>
    </li>

    <!-- Heading -->
    <br><div class="sidebar-heading">
        <strong>Perbaikan & Perawatan</strong>
    </div>

    <!-- Nav Item - Perbaikan -->
    <li class="nav-item {{ request()->routeIs('kondisi-error') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kondisi-error') }}">
            <i class="fas fa-fw {{ request()->routeIs('kondisi-error') ? 'fa-tools' : 'fa-wrench' }}"></i>
            <span>Perbaikan</span>
        </a>
    </li>

    <!-- Nav Item - Kerusakan -->
    <li class="nav-item {{ request()->routeIs('kondisi-rusak') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kondisi-rusak') }}">
            <i class="fas fa-fw {{ request()->routeIs('kondisi-rusak') ? 'fa-exclamation-circle' : 'fa-exclamation-triangle' }}"></i>
            <span>Kerusakan</span>
        </a>
    </li>

    <!-- Nav Item - Perawatan -->
    <li class="nav-item {{ request()->routeIs('perawatan.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('perawatan.adminmtc') }}">
            <i class="fas fa-fw {{ request()->routeIs('perawatan.adminmtc') ? 'fa-cogs' : 'fa-cog' }}"></i>
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