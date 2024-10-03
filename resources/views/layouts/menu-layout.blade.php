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
    <title>{{ $title }} - Menu </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skin_color.css') }}">
    {{-- @vite(['resources/js/app.js', 'resources/sass/app.scss', 'resources/css/app.css']) --}}
</head>

<body class="hold-transition light-skin sidebar-mini theme-primary fixed">
    <header class="main-header">
        <nav class="navbar m-0 navbar-static-top justify-content-end">
            <div class="navbar-custom-menu r-side">
                <ul class="nav navbar-nav">
                    <li class="dropdown notifications-menu">
                        @include('layouts.partials.notification', ['notification' => $notification])
                    </li>
                    <!-- User Account-->
                    <li class="dropdown user user-menu">
                        <a href="#" class="waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown"
                            title="User">
                            <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                        </a>
                        <ul class="dropdown-menu animated flipInX">
                            <li class="user-body">
                                <a class="dropdown-item" href="#"><i class="ti-user text-muted me-2"></i>
                                    Profile</a>
                                <div class="dropdown-divider"></div>
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
    </div>



    <!-- Vendor JS -->
    <script src="{{ asset('assets/vendor_plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('js/vendors.min.js') }}"></script>
    <script src="{{ asset('js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
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
