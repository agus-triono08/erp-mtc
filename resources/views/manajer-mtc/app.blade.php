
<!DOCTYPE html>
<html lang="en">

<!--Head-->
@include('manajer-mtc.head')

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('manajer-mtc.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <div class="sticky-top" >
                <!-- Topbar -->
                @include('manajer-mtc.topbar')
                <!-- End of Topbar -->
                </div>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Main Content -->
                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('manajer-mtc.footer')
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    @include('manajer-mtc.scroll-to-top-button')

    <!-- Logout Modal-->
    @include('manajer-mtc.logout-modal')
    
    <!--JavaScript-->
    @include('manajer-mtc.js')

</body>

</html>