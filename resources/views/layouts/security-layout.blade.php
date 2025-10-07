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
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/nestable/nestable.css') }}">
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
        <div class="container-full" style="padding: 5rem;">
            <section class="content">
                @yield('content')
            </section>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/vendor_plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/apexcharts-bundle-new/dist/apexcharts.js') }}"></script>

    <!-- Vendor JS -->
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
    <script src="{{ asset('assets/vendor_components/fullcalendar/fullcalendar.js') }}"></script>
    <script src="{{ asset('assets/vendor_plugins/polyfill/datetime-polyfill.js') }}"></script>

    <!-- EduAdmin App -->
    <script src="{{ asset('js/template.js') }}"></script>
    <script>
        // let base_url = "{{ route('root') }}";
        let base_url = window.location.origin;
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>

    @if ($page == 'security-index')
        @vite(['resources/js/pages/security/index.js'])
    @endif
</body>

</html>
