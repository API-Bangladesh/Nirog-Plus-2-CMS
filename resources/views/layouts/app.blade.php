<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags -->
    <base href="{{ asset('/') }}"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Wieldy - A fully responsive, HTML5 based admin template">
    <meta name="keywords" content="Responsive, HTML5, admin theme, business, professional, jQuery, web design, CSS3, sass">
    <!-- /meta tags -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('settings.title') ? config('settings.title') : env('APP_NAME') }} - @yield('title')</title>

    <!-- Site favicon -->
<link rel="shortcut icon" href="{{ url(config('app.url') . '/public/storage/logo/' . config('settings.favicon')) }}" type="image/x-icon">

    <!-- /site favicon -->

    <link rel="stylesheet" href="css/app.css">
    @if(request()->path() !== '/')
    <link rel="stylesheet" href="css/daterangepicker.css">
    <link rel="stylesheet" href="css/datatables.bundle.css">
    <script src="js/webcam.min.js" crossorigin="anonymous"></script>
    @endif
    @stack('stylesheet')
</head>
<body class="dt-sidebar--fixed dt-header--fixed">

<!-- Loader -->
<div class="dt-loader-container">
    <div class="dt-loader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"></circle>
        </svg>
    </div>
</div>
<!-- /loader -->

<!-- Root -->
<div class="dt-root">
    <!-- Header -->
@include('include.header')
<!-- /header -->

    <!-- Site Main -->
    <main class="dt-main">
        <!-- Sidebar -->
        <x-sidebar/>
        <!-- /sidebar -->

        <!-- Site Content Wrapper -->
        <div class="dt-content-wrapper">

            <!-- Site Content -->
        @yield('content')
        <!-- /site content -->

            <!-- Footer -->
        @include('include.footer')
        <!-- /footer -->

        </div>
        <!-- /site content wrapper -->

    </main>
</div>
<!-- /root -->

<!-- Optional JavaScript -->

<script src="js/app.js" crossorigin="anonymous"></script>
<script src="js/moment.min.js" crossorigin="anonymous"></script>
<script src="js/perfect-scrollbar.min.js" crossorigin="anonymous"></script>
@if(request()->path() !== '/')
<script src="js/daterangepicker.min.js" crossorigin="anonymous"></script>
<script src="js/datatables.bundle.js" crossorigin="anonymous"></script>
@endif
<!-- /perfect scrollbar jQuery -->

<script src="js/script.js" crossorigin="anonymous"></script>

<script src="js/custom.js" crossorigin="anonymous"></script>
{{-- <script src="js/custom/charts/dashboard-crypto.js"></script> --}}
<script>
    var _token = "{{ csrf_token() }}";
</script>
@stack('script')
</body>
</html>