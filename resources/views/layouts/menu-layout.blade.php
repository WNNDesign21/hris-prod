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

<div class="wrapper">
    <header class="main-header">
        <nav class="navbar m-0 navbar-static-top justify-content-end">
            <div class="navbar-custom-menu r-side">
                <ul class="nav navbar-nav">
                    <li class="dropdown notifications-menu">
                        <a href="#" class="btn btn-light dropdown-toggle position-relative"
                            data-bs-toggle="dropdown" title="Notifications">
                            <i class="icon-Notifications"><span class="path1"></span><span class="path2"></span></i>
                            @if ($notification['count_notif'] > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 1rem;z-index:2;">
                                    {{ $notification['count_notif'] }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu animated bounceIn" style="min-width:300px;max-width:350px;">
                            <li class="header">
                                <div class="p-20">
                                    <div class="flexbox">
                                        <div>
                                            <h4 class="mb-0 mt-0">Notifications</h4>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu sm-scroll">
                                    @if (!empty($notification['list']))
                                        @foreach ($notification['list'] as $list)
                                            <li>
                                                @if (auth()->user()->hasRole('personalia') || auth()->user()->hasRole('super user') || auth()->user()->hasRole('atasan'))
                                                    <a href="{{ route('master-data.kontrak') }}"
                                                        style="white-space: normal;">
                                                        <i class="fa fa-user text-danger"></i> {{ $list['nama'] }}
                                                        memiliki
                                                        sisa
                                                        <strong>{{ $list['jumlah_hari'] }} Hari</strong> sebelum masa
                                                        <strong>TERMINASI</strong>.
                                                    </a>
                                                @else
                                                    <a href="#"
                                                        style="pointer-events: none; cursor: default;white-space: normal;">
                                                        <i class="fa fa-user text-danger"></i> Anda memiliki sisa
                                                        <strong>{{ $list['jumlah_hari'] }} Hari</strong> sebelum masa
                                                        <strong>TERMINASI</strong>, segera hubungi atasan anda.
                                                    </a>
                                                @endif
                                            </li>
                                        @endforeach
                                    @endif
                                    @if (!empty($notification['cutie_approval']))
                                        @foreach ($notification['cutie_approval'] as $cutie_approval)
                                            <li>
                                                @if (auth()->user()->hasRole('personalia') || auth()->user()->hasRole('super user'))
                                                    <a href="{{ route('cutie.personalia-cuti') }}"
                                                        style="white-space: normal;">
                                                        <i class="fa fa-user text-danger"></i>Pengajuan Cuti
                                                        {{ $cutie_approval['nama'] }}
                                                        menunggu Legalized oleh HRD
                                                        <br>
                                                        <strong>{{ $cutie_approval['jumlah_hari'] }} Hari</strong>
                                                        sebelum
                                                        otomatis
                                                        <strong>REJECTED</strong>.
                                                    </a>
                                                @elseif (auth()->user()->hasRole('atasan'))
                                                    <a href="{{ route('cutie.member-cuti') }}"
                                                        style="white-space: normal;">
                                                        <i class="fa fa-user text-danger"></i>Pengajuan Cuti
                                                        {{ $cutie_approval['nama'] }}
                                                        menunggu Check / Approve oleh Atasan
                                                        <br>
                                                        <strong>{{ $cutie_approval['jumlah_hari'] }} Hari</strong>
                                                        sebelum
                                                        otomatis
                                                        <strong>REJECTED</strong>.
                                                    </a>
                                                @else
                                                    <a href="{{ route('cutie.pengajuan-cuti') }}"
                                                        style="white-space: normal;">
                                                        <i class="fa fa-user text-danger"></i>Pengajuan Cuti
                                                        {{ $cutie_approval['jenis_cuti'] }} dengan durasi
                                                        {{ $cutie_approval['durasi_cuti'] }} Hari, masih menunggu
                                                        persetujuan,<br> segera follow-up atasan & HRD untuk
                                                        menindaklanjuti!
                                                        <br>
                                                        <strong>{{ $cutie_approval['jumlah_hari'] }} Hari</strong>
                                                        sebelum
                                                        otomatis
                                                        <strong>REJECTED</strong>.
                                                    </a>
                                                @endif
                                            </li>
                                        @endforeach
                                    @endif
                                    @if (!empty($notification['rejected_cuti']))
                                        @foreach ($notification['rejected_cuti'] as $rejected_cuti)
                                            <li>
                                                <a href="{{ route('cutie.pengajuan-cuti') }}"
                                                    style="white-space: normal;">
                                                    <i class="fa fa-user text-danger"></i>Pengajuan Cuti
                                                    {{ $rejected_cuti['jenis_cuti'] }} dengan durasi
                                                    {{ $cutie_approval['durasi_cuti'] }} Hari
                                                    <strong>DITOLAK</strong> pada
                                                    {{ \Carbon\Carbon::parse($rejected_cuti['rejected_at'])->format('Y-m-d H:i:s') }}
                                                    <br>
                                                    Pesan ini akan otomatis terhapus H+3 sejak Rencana Mulai Cuti
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                    @if (empty($notification['list']) && empty($notification['cutie_approval']) && empty($notification['rejected_cuti']))
                                        <li>
                                            <a href="#" class="text-center">
                                                Everything is just Fine 👌
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        </ul>
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
        <div class="row align-items-center justify-content-md-center h-p100" style="margin-top:-100px;">
            <div class="col-12">
                <div class="row justify-content-center g-3">
                    @yield('content')
                </div>
            </div>
        </div>
        @include('pages.cuti-e.modal-event-cuti')
    </div>
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
