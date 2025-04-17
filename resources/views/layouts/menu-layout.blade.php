@php
    $title = config('app.name');
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <title>{{ $pageTitle }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skin_color.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/animate/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/lightbox-master/dist/ekko-lightbox.min.css') }}">
    @vite(['resources/css/app.css'])
</head>

<body class="hold-transition light-skin sidebar-mini theme-primary fixed">
    <header class="main-header">
        <nav class="navbar m-0 navbar-static-top">
            <div class="app-menu">
                <h4 class="mb-0 text-primary"><i class="ti-user"></i>
                    {{ auth()->user()->hasAnyRole(['atasan', 'member'])? auth()->user()->karyawan->nama: (auth()->user()->hasRole('personalia')? 'PERSONALIA': 'SECURITY') }}
                </h4>
            </div>
            <div class="navbar-custom-menu r-side">
                <ul class="nav navbar-nav">
                    <li class="dropdown notifications-menu">
                        @include('layouts.partials.notification', ['notification' => $notification])
                    </li>
                    <!-- User Account-->
                    <li class="dropdown user user-menu">
                        <a href="#" class="btn btn-light dropdown-toggle position-relative"
                            data-bs-toggle="dropdown" title="User">
                            <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                            @if ($notification['agenda_lembur'] > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 1rem;z-index:2;">
                                    <i class="ti-bell"></i>
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu animated flipInX">
                            <li class="user-body">
                                @hasanyrole(['atasan', 'member'])
                                    <a class="dropdown-item btnProfile" href="#"><i
                                            class="ti-user text-muted me-2"></i>
                                        Profile</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item btnKontrak" href="#"><i
                                            class="ti-write text-muted me-2"></i>
                                        Kontrak</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item btnKSK" href="#"><i class="ti-pencil text-muted me-2"></i>
                                        KSK</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item btnAgendaLembur position-relative" href="#"><i
                                            class="ti-agenda text-muted me-2"></i>
                                        Agenda Lembur
                                        @if ($notification['agenda_lembur'] > 0)
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                style="font-size: 1rem;z-index:2;">
                                                {{ $notification['agenda_lembur'] }}
                                            </span>
                                        @endif
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item btnLembur" href="#"><i
                                            class="ti-time text-muted me-2"></i>
                                        Slip Lembur
                                    </a>
                                    <div class="dropdown-divider"></div>
                                @endhasanyrole
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                    <i class="ti-lock text-muted me-2"></i>{{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="container h-p100">
        <div class="row align-items-center justify-content-md-center h-p100" id="menu-container">
            <div class="col-12">
                <div class="row justify-content-center g-3">
                    @yield('content')
                </div>
            </div>
        </div>
        @include('pages.cuti-e.modal-event-cuti')
        @include('pages.menu.modal-profile')
        @include('pages.menu.modal-kontrak')
        @include('pages.menu.modal-lembur')
        @include('pages.menu.modal-agenda-lembur')
        @include('pages.menu.modal-ksk')
    </div>



    <!-- Vendor JS -->
    <script src="{{ asset('assets/vendor_plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('js/vendors.min.js') }}"></script>
    <script src="{{ asset('js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
    </script>

    <script src="{{ asset('assets/vendor_components/lightbox-master/dist/ekko-lightbox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/Magnific-Popup-master/dist/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/Magnific-Popup-master/dist/jquery.magnific-popup-init.js') }}">
    </script>
    <script src="{{ asset('assets/vendor_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/fullcalendar/fullcalendar.js') }}"></script>
    <script>
        // let base_url = "{{ route('root') }}";
        let base_url = window.location.origin;
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>

    @vite(['resources/js/pages/menu.js'])
</body>

</html>
