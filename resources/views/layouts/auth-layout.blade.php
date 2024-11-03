<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
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
        @yield('navbar')
        <div class="content-wrapper">
            <div class="container-full">
                <section class="content">
                    @yield('content')
                </section>
            </div>
        </div>
        @include('layouts.footer')
        {{-- @include('layouts.control-sidebar') --}}
    </div>
    {{-- @include('layouts.side-panel') --}}

    <!-- jQuery -->
    <script src="{{ asset('assets/vendor_plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/apexcharts-bundle/dist/apexcharts.js') }}"></script>

    <!-- Vendor JS -->
    <script src="{{ asset('js/vendors.min.js') }}"></script>
    <script src="{{ asset('js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/vendor_components/bootstrap-select/dist/js/bootstrap-select.js') }}"></script> --}}
    <script src="{{ asset('assets/vendor_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/lightbox-master/dist/ekko-lightbox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/Magnific-Popup-master/dist/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/Magnific-Popup-master/dist/jquery.magnific-popup-init.js') }}">
    </script>
    <script src="{{ asset('assets/vendor_components/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/nestable/jquery.nestable.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/fullcalendar/fullcalendar.js') }}"></script>

    <!-- EduAdmin App -->
    <script src="{{ asset('js/template.js') }}"></script>
    {{-- <script src="{{ asset('js/pages/dashboard4.js') }}"></script> --}}
    {{-- <script src="{{ asset('js/pages/advanced-form-element.js') }}"></script> --}}
    <script>
        // let base_url = "{{ route('root') }}";
        let base_url = window.location.origin;
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>

    @if ($page == 'masterdata-dashboard')
        @vite(['resources/js/pages/master-data-dashboard.js'])
    @endif

    @if ($page == 'masterdata-organisasi')
        @vite(['resources/js/pages/organisasi.js'])
    @endif

    @if ($page == 'masterdata-divisi')
        @vite(['resources/js/pages/divisi.js'])
    @endif

    @if ($page == 'masterdata-departemen')
        @vite(['resources/js/pages/departemen.js'])
    @endif

    @if ($page == 'masterdata-seksi')
        @vite(['resources/js/pages/seksi.js'])
    @endif

    @if ($page == 'masterdata-grup')
        @vite(['resources/js/pages/grup.js'])
    @endif

    @if ($page == 'masterdata-jabatan')
        @vite(['resources/js/pages/jabatan.js'])
    @endif

    @if ($page == 'masterdata-posisi')
        @vite(['resources/js/pages/posisi.js'])
        <script src="{{ asset('js/pages/nestable.js') }}"></script>
    @endif

    @if ($page == 'masterdata-karyawan')
        @vite(['resources/js/pages/karyawan.js'])
    @endif

    @if ($page == 'masterdata-kontrak')
        @vite(['resources/js/pages/kontrak.js'])
    @endif

    @if ($page == 'masterdata-turnover')
        @vite(['resources/js/pages/turnover.js'])
    @endif

    @if ($page == 'masterdata-export')
        @vite(['resources/js/pages/export.js'])
    @endif

    @if ($page == 'masterdata-template')
        @vite(['resources/js/pages/template.js'])
    @endif

    @if ($page == 'masterdata-event')
        @vite(['resources/js/pages/event.js'])
    @endif

    @if ($page == 'cutie-dashboard')
        @vite(['resources/js/pages/cutie-dashboard.js'])
    @endif

    @if ($page == 'cutie-pengajuan-cuti')
        @vite(['resources/js/pages/cutie-pengajuan-cuti.js'])
    @endif

    @if ($page == 'cutie-member-cuti')
        @vite(['resources/js/pages/cutie-member-cuti.js'])
    @endif

    @if ($page == 'cutie-personalia-cuti')
        @vite(['resources/js/pages/cutie-personalia-cuti.js'])
    @endif

    @if ($page == 'cutie-dashboard')
        @vite(['resources/js/pages/cutie-dashboard.js'])
    @endif

    @if ($page == 'cutie-export')
        @vite(['resources/js/pages/cutie-export.js'])
    @endif

    @if ($page == 'cutie-setting')
        @vite(['resources/js/pages/cutie-setting.js'])
    @endif

    @if ($page == 'cutie-bypass-cuti')
        @vite(['resources/js/pages/cutie-bypass-cuti.js'])
    @endif

    @if ($page == 'lembure-pengajuan-lembur')
        @vite(['resources/js/pages/lembure-pengajuan-lembur.js'])
    @endif

    @if ($page == 'lembure-approval-lembur')
        @vite(['resources/js/pages/lembure-approval-lembur.js'])
    @endif

    @if ($page == 'lembure-setting-upah-lembur')
        @vite(['resources/js/pages/lembure-setting-upah-lembur.js'])
    @endif

</body>

</html>
