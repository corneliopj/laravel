@include('layouts.partials.head')
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    {{-- Top Navbar --}}
    @include('layouts.partials.navbar')

    {{-- Main Sidebar Container --}}
    @include('layouts.partials.sidebar')

    {{-- Content Wrapper. Contains page content --}}
    <div class="content-wrapper">
        @yield('content')
    </div>

    {{-- Footer --}}
    @include('layouts.partials.footer')
</div>
<!-- ./wrapper -->

{{-- Global Scripts --}}
@include('layouts.partials.scripts')
</body>
</html>
