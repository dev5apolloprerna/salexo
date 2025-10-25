<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">
{{-- Include Head --}}
@include('common.superadmin.head')

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- Topbar -->
        @include('common.superadmin.header')
        <!-- End of Topbar -->

        <!-- Sidebar -->
        @include('common.client_sidebar')
        <!-- End of Sidebar -->

        @yield('content')

        @include('common.superadmin.footer')

    </div>

    @include('common.superadmin.footerjs')

    @yield('scripts')

</body>

</html>
