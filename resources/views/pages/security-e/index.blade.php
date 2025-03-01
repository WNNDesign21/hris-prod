@extends('layouts.security-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    <header class="main-header">
        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top">
            <div class="app-menu">
                <ul class="header-megamenu nav">
                    <li class="btn-group nav-item d-md-none">
                        <a href="#" class="waves-effect waves-light nav-link push-btn" data-toggle="push-menu"
                            role="button">
                            <span class="icon-Align-left"><span class="path1"></span><span class="path2"></span><span
                                    class="path3"></span></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="navbar-custom-menu r-side">
                <ul class="nav navbar-nav gap-1">
                    <li class="dropdown user user-menu">
                        <a href="#" class="btn btn-light dropdown-toggle position-relative" data-bs-toggle="dropdown"
                            title="User">
                            <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                        </a>
                        <ul class="dropdown-menu animated flipInX">
                            <li class="user-body">
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
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-4 col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-center">
                    <div class="row">
                        <h2>SCAN QR</h3>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-12 d-flex justify-content-center">
                            <div id="qr-scanner" style="min-width: 500px; height:100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Log Book Izin</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info waves-effect btnReloadIzin"><i
                                    class="fas fa-sync-alt"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="izin-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Departemen</th>
                                    <th>Posisi</th>
                                    <th>Rencana</th>
                                    <th>Aktual</th>
                                    <th>Jenis Izin</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Log Book TL</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info waves-effect btnReloadTl"><i
                                    class="fas fa-sync-alt"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="tl-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Karyawan</th>
                                    <th>Tanggal</th>
                                    <th>Kendaraan</th>
                                    <th>Pergi</th>
                                    <th>Kembali</th>
                                    <th>KM Awal</th>
                                    <th>KM Akhir</th>
                                    <th>KM Selisih</th>
                                    <th>Rute</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Checked</th>
                                    <th>Legalized</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pages.security-e.modal-qr-scanner')
@endsection
