<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">
{{-- Include Head --}}
@include('common.superadmin.head')

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- Topbar -->
        @include('common.client_header')
        <!-- End of Topbar -->

        <!-- Sidebar -->
        @include('common.client_sidebar')
        <!-- End of Sidebar -->

        @yield('content')

        @include('common.superadmin.footer')

    </div>

    @include('common.superadmin.footerjs')

    {{-- From/To date filters: any date can be picked (including previous months).
         If the range becomes reversed, the other date is moved to match instead of
         blocking the picker with min/max. --}}
    <script>
        document.addEventListener('change', function (e) {
            var el = e.target;
            if (!el.form || el.type !== 'date' || (el.name !== 'from_date' && el.name !== 'to_date')) return;
            var from = el.form.querySelector('input[name="from_date"]');
            var to = el.form.querySelector('input[name="to_date"]');
            if (!from || !to || !from.value || !to.value || from.value <= to.value) return;
            if (el === from) { to.value = from.value; } else { from.value = to.value; }
        });
    </script>

    @yield('scripts')

</body>

</html>
