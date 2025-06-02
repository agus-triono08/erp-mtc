<nav class="navbar navbar-expand navbar-light bg-white topbar shadow position-fixed" style="width: 100%;">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <div class="text-center d-none d-md-inline">
        <i id="sidebarToggle" class="fas fa-fw fa-bars gray"></i>
    </div>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav" style="margin-left:auto!important; min-width:650px;">

        <!-- Nav Item Report -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link" href="https://docs.google.com/forms/d/e/1FAIpQLSeuESWTp3A3C39v86JGDB9uGcWohM-hpsGqCxTyadv2BTvzZg/viewform" target="_blank">
                <i class="fas fa-fw fa-exclamation-triangle" style="color: #ffac32"></i>
                <span>Lapor Kendala Teknis</span>
            </a>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    @auth
                        {{ Auth::user()->karyawan->nama }} 
                        {{-- ({{ Auth::user()->jabatan->nama ?? 'Unknown Role' }}) --}}
                    @else
                        Guest
                    @endauth
                </span>
                <img class="img-profile rounded-circle"
                    src="{{ Auth::user()->foto ?  url('/api/user/profile/' . Auth::user()->foto) : asset('vendor/sb-admin/img/undraw_profile.svg') }}">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#profileModal">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>

<!-- Logout Modal-->
<div class="modal show" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div class="modal show" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">User Profile</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                @auth
                <div class="text-center mb-3">
                    <img class="img-profile rounded-circle" width="100"
                        src="{{ Auth::user()->foto ?  url('/api/user/profile/' . Auth::user()->foto) : asset('vendor/sb-admin/img/undraw_profile.svg') }}">
                </div>
                <div class="form-group">
                    <label>Nama:</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->karyawan->nama }}" readonly>
                </div>
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->username }}" readonly>
                </div>
                {{-- <div class="form-group">
                    <label>Divisi:</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->divisi->nama ?? 'N/A' }}" readonly>
                </div>
                <div class="form-group">
                    <label>Jabatan:</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->jabatan->nama ?? 'N/A' }}" readonly>
                </div> --}}
                @else
                <p>No user data available</p>
                @endauth
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>