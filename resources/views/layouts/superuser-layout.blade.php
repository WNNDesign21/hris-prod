<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon"
        href="{{ file_exists(public_path('storage/system/setting/favicon.ico')) ? asset('storage/system/setting/favicon.ico') : asset('favicon.ico') }}">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor_plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/sweetalert2/dist/sweetalert2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/datatable/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/animate/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/lightbox-master/dist/ekko-lightbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor_plugins/select2-theme/select2-bootstrap-5-theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skin_color.css') }}">
    @vite(['resources/css/app.css'])
</head>

<body class="hold-transition light-skin sidebar-mini theme-primary fixed">
    <div class="wrapper">
        <div id="loader"></div>
        @yield('header')
        @yield('navbar')
        <div class="content-wrapper">
            <div class="container-full">
                <section class="content">
                    @yield('content')
                </section>
            </div>
        </div>
        @include('layouts.footer')
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/vendor_plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('js/vendors.min.js') }}"></script>
    <script src="{{ asset('js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/lightbox-master/dist/ekko-lightbox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/Magnific-Popup-master/dist/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/Magnific-Popup-master/dist/jquery.magnific-popup-init.js') }}">
    </script>
    <script src="{{ asset('assets/vendor_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/nestable/jquery.nestable.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/jquery-steps-master/build/jquery.steps.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/jquery-validation-1.17.0/dist/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/template.js') }}"></script>
    <script>
        let base_url = window.location.origin;
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>

    @if ($page == 'superuser-organisasi')
        @vite(['resources/js/pages/superuser/organisasi.js'])
    @endif

    @if ($page == 'superuser-divisi')
        @vite(['resources/js/pages/superuser/divisi.js'])
    @endif

    @if ($page == 'superuser-departemen')
        @vite(['resources/js/pages/superuser/departemen.js'])
    @endif

    @if ($page == 'superuser-seksi')
        @vite(['resources/js/pages/superuser/seksi.js'])
    @endif

    @if ($page == 'superuser-jabatan')
        @vite(['resources/js/pages/superuser/jabatan.js'])
    @endif

    @if ($page == 'superuser-user')
        @vite(['resources/js/pages/superuser/user.js'])
    @endif

    @if ($page == 'superuser-activity-log')
        @vite(['resources/js/pages/superuser/activity-log.js'])
    @endif

    @if ($page == 'superuser-setting')
        @vite(['resources/js/pages/superuser/setting.js'])
    @endif
</body>

</html>
