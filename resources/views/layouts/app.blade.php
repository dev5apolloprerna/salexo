<!doctype html>
<html lang="en" data-layout="horizontal" data-layout-style="default" data-layout-position="fixed" data-topbar="light"
    data-sidebar="dark" data-sidebar-size="sm-hover" data-layout-width="fluid">

{{-- Include Head --}}
@include('common.superadmin.head')

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- Topbar -->
        @include('common.superadmin.header')
        <!-- End of Topbar -->

        <!-- Sidebar -->
        @include('common.superadmin.sidebar')
        <!-- End of Sidebar -->

        @yield('content')

        @include('common.superadmin.footer')

    </div>

    @include('common.superadmin.footerjs')

    @yield('scripts')

</body>

</html>
