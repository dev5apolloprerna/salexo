<!DOCTYPE html>
<html lang="en">

{{-- Include Head --}}
@include('common.front.fronthead')

<body id="page-top">

    <div class="page-wrapper">

        <div class="container py-5">
        @include('common.front.frontheader')


        @yield('content')

        @include('common.front.frontfooter')
</div>


        @include('common.front.frontfooterjs')

        @yield('scripts')
    </div>
</body>

</html>