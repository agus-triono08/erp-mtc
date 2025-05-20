<ul class="navbar-nav bg-gradient-white sidebar sidebar-dark accordion" id="accordionSidebar"
    style="position: sticky; top: 0; height: 100vh; z-index: 1020;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard.adminmtc') }}">
        <div class="sidebar-brand-icon"
            style="background-color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; width: 50px; height: 50px;">
            <img src="https://image1ws.indotrading.com/s3/webp/co48220/companylogo/w200-h200/sinkoprimaalloy3ecab9ce-ecdf-4b3a-b6d0-db7126ae03f4.png" 
                alt="LOGO" 
                style="width: 30px; height: 30px; object-fit: contain;">
        </div>
        <div class="sidebar-brand-text mx-1" style="text-transform: none; white-space: nowrap; font-size: 13px;">Sinko Prima Alloy</div>
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
            <div class="spinner-border spinner-border-sm d-none" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </a>
    </li>

    <!-- Nav Item - History -->
    <li class="nav-item {{ request()->routeIs('adminmtc-riwayat') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('adminmtc-riwayat') }}">
            <i class="fas fa-fw {{ request()->routeIs('adminmtc-riwayat') ? 'fa-folder-open' : 'fa-history' }}"></i>
            <span>Riwayat</span>
            <div class="spinner-border spinner-border-sm d-none" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </a>
    </li>

    {{-- Nav Item - Data Hilang --}}
    <li class="nav-item {{ request()->routeIs('datahilang.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('datahilang.adminmtc') }}">
            <i class="bi {{ request()->routeIs('datahilang.adminmtc') ? 'bi-repeat' : 'bi-arrow-repeat' }}"></i>
            <span>Penggantian Alat/Mesin</span>
        </a>
    </li>

    <!-- Heading -->
    <br><div class="sidebar-heading">
        <strong>Inventory Alat/Mesin</strong>
    </div>

    <!-- Nav Item - Data -->
    <li class="nav-item {{ request()->routeIs('kategori.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kategori.adminmtc') }}">
            <i class="bi {{ request()->routeIs('kategori.adminmtc') ? 'bi-file-spreadsheet' : 'bi-file-spreadsheet-fill' }}"></i>
            <span>Kategori/Merek/Tipe</span>
        </a>
    </li>
    
    <!-- Heading -->
    <br><div class="sidebar-heading">
        <strong>Lokasi Penyimpanan</strong>
    </div>

    <!-- Nav Item - Data -->
    <li class="nav-item {{ request()->routeIs('layout.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('layout.adminmtc') }}">
            <i class="bi {{ request()->routeIs('layout.adminmtc') ? 'bi-door-open-fill' : 'bi-door-closed-fill' }}"></i>
            <span>Layout</span>
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
        <strong>Jadwal Perawatan</strong>
    </div>

    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#jadwal-collapse" aria-expanded="true" aria-controls="jadwal-collapse">
            <i class="bi bi-calendar-check "></i>
            <span>Jadwal Perawatan</span>
        </a>
        <ul id="jadwal-collapse" class="collapse" aria-labelledby="jadwal-collapse" data-parent="#accordionSidebar">
            <li class="nav-item {{ request()->routeIs('perencanaanjadwalperawatan.adminmtc') ? 'active' : '' }}">
                <a class="nav-link" href="{{route('perencanaanjadwalperawatan.adminmtc')}}">
                    {{-- <i class="fas fa-fw fa-bug" style="color: #169ea8;"></i> --}} Perencanaan
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('jadwalperawatan.adminmtc') ? 'active' : '' }}">
                <a class="nav-link" href="{{route('jadwalperawatan.adminmtc')}}">
                    {{-- <i class="fas fa-fw fa-exclamation-triangle" style="color: #169ea8;"></i> --}} Pelaksanaan 
                </a>
            </li>
        </ul>
    </li>

    <!-- Nav Item - Perawatan -->
    <li class="nav-item {{ request()->routeIs('riwayatperawatan.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('riwayatperawatan.adminmtc') }}">
            <i class="bi {{ request()->routeIs('riwayatperawatan.adminmtc') ? 'bi-clock-history' : 'bi-clock-fill' }}"></i>
            <span>Riwayat Perawatan</span>
        </a>
    </li>

    <!-- Heading -->
    <br><div class="sidebar-heading">
        <strong>Perbaikan & Kerusakan</strong>
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
    {{-- <li class="nav-item {{ request()->routeIs('perawatan.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('perawatan.adminmtc') }}">
            <i class="fas fa-fw {{ request()->routeIs('perawatan.adminmtc') ? 'fa-cogs' : 'fa-cog' }}"></i>
            <span>Perawatan</span>
        </a>
    </li> --}}

    <!-- Nav Item - Perawatan -->
    {{-- <li class="nav-item {{ request()->routeIs('jadwalperawatan.adminmtc') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('jadwalperawatan.adminmtc') }}">
            <i class="bi {{ request()->routeIs('jadwalperawatan.adminmtc') ? 'bi-calendar-check' : 'bi-calendar-check-fill' }}"></i>
            <span>Jadwal Perawatan</span>
        </a>
    </li> --}}    

    <!-- Heading -->
    {{-- <br><div class="sidebar-heading">
        <strong>Pengantian</strong>
    </div> --}}    

    <!-- Divider -->
    <!--<hr class="sidebar-divider d-none d-md-block">-->

    <!-- Sidebar Toggler (Sidebar) -->
    <!--<div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>-->

</ul>