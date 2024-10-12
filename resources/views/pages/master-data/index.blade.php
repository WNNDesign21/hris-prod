@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-masterdata')
@endsection

@section('content')
    {{-- JUMLAH DASHBOARD --}}
    <div class="row">
        <div class="col-12">
            <div class="box no-shadow mb-0 bg-transparent">
                <div class="box-header no-border px-0">
                    <h4 class="box-title">Dashboard Master Data Karyawan</h4>
                    {{-- <ul class="box-controls pull-right d-md-flex d-none">
                        <li>
                            <button class="btn btn-primary-light px-10">View All</button>
                        </li>
                    </ul> --}}
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-12">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Data Karyawan <br><small>{{ \Carbon\Carbon::now()->format('F Y') }}</small></h4>
                </div>
                <div class="box-body px-0 pt-0">
                    <div class="media-list media-list-hover">
                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="aktif_karyawan"><i
                                    class="fas fa-sync-alt fa-spin fs-24"></i></h4>
                            <div class="media-body ps-15 bs-5 rounded border-success">
                                <h5>AKTIF</h5>
                            </div>
                        </a>

                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="habis_kontrak_karyawan"><i
                                    class="fas fa-sync-alt fa-spin fs-24"></i></h4>
                            <div class="media-body ps-15 bs-5 rounded border-primary">
                                <h5>HABIS KONTRAK</h5>
                            </div>
                        </a>

                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="mengundurkan_diri_karyawan"><i
                                    class="fas fa-sync-alt fa-spin fs-24"></i></h4>
                            <div class="media-body ps-15 bs-5 rounded border-danger">
                                <h5>MENGUNDURKAN DIRI</h5>
                            </div>
                        </a>

                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="pensiun_karyawan"><i
                                    class="fas fa-sync-alt fa-spin fs-24"></i></h4>
                            <div class="media-body ps-15 bs-5 rounded border-info">
                                <h5>PENSIUN</h5>
                            </div>
                        </a>

                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="terminasi_karyawan"><i
                                    class="fas fa-sync-alt fa-spin fs-24"></i></h4>
                            <div class="media-body ps-15 bs-5 rounded border-info">
                                <h5>TERMINASI</h5>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-12">
            <div class="box">
                <div class="box-body">
                    <h3 class="mt-0 mb-5">Turnover Karyawan {{ date('Y') }}</h3>
                    <p class="text-fade">Total Karyawan Keluar : {{ $jumlah_karyawan_keluar }}</p>
                    <div style="min-height: 198px;">
                        <div id="turnover-chart">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- GRAFIK DASHBOARD --}}
    <div class="row">
        <div class="col-xl-6 col-12">
            <div class="box">
                <div class="box-body">
                    <h3 class="mt-0 mb-5">Turnover Detail {{ date('Y') }}</h3>
                    <div style="min-height: 198px;">
                        <div id="turnover-detail-chart">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-12">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Kontrak Progress</h4>
                </div>
                <div class="box-body">
                    <div style="min-height: 198px;">
                        <div id="kontrak-progress-chart"></div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <p class="d-flex align-items-center fw-600 mx-20"><span
                                class="badge badge-xl badge-dot badge-warning me-20"></span> On Progress
                        </p>
                        <p class="d-flex align-items-center fw-600 mx-20"><span
                                class="badge badge-xl badge-dot badge-primary me-20"></span> Done</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-12 mb-4">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Total Data by Status <br><small>2017 - {{ date('Y') }}</small></h4>
                </div>
                <div class="box-body">
                    <div style="min-height: 198px;">
                        <div id="total-data-by-status-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
