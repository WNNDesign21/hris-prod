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
        <div class="col-xl-4 col-12 mb-4">
            <div class="box" style="height: 100%;">
                <div class="box-header with-border">
                    <h4 class="box-title">Karyawan Data <br><small>February 2024</small></h4>
                </div>
                <div class="box-body px-0 pt-0 pb-10">
                    <div class="media-list media-list-hover">
                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="aktif_karyawan">6</h4>
                            <div class="media-body ps-15 bs-5 rounded border-success">
                                <p>AKTIF</p>
                                <span class="text-fade">Last Updated</span>
                            </div>
                        </a>

                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="terminasi_karyawan">1</h4>
                            <div class="media-body ps-15 bs-5 rounded border-primary">
                                <p>TERMINASI</p>
                                <span class="text-fade">Last Updated</span>
                            </div>
                        </a>

                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="resign_karyawan">4</h4>
                            <div class="media-body ps-15 bs-5 rounded border-danger">
                                <p>RESIGN</p>
                                <span class="text-fade">Last Updated</span>
                            </div>
                        </a>

                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="pensiun_karyawan">3</h4>
                            <div class="media-body ps-15 bs-5 rounded border-info">
                                <p>PENSIUN</p>
                                <span class="text-fade">Last Updated</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-12 mb-4">
            <div class="box" style="height: 100%;">
                <div class="box-body">
                    <h3 class="mt-0 mb-5">Employee Turnover</h3>
                    <p class="text-fade">Jan - Dec {{ date('Y') }}</p>
                    {{-- <p class="text-fade">400/500 <small class="text-danger"><i class="fa fa-arrow-down"></i>
                            15%</small></p> --}}
                    <div id="turnover-chart"></div>
                </div>
            </div>
        </div>
    </div>
    {{-- GRAFIK DASHBOARD --}}
    <div class="row">
        <div class="col-xl-3 col-12 mb-4">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Working Hours</h4>
                    <ul class="box-controls pull-right d-md-flex d-none">
                        <li class="dropdown">
                            <button class="dropdown-toggle btn btn-warning-light px-10" data-bs-toggle="dropdown"
                                href="#">Today</button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item active" href="#">Today</a>
                                <a class="dropdown-item" href="#">Yesterday</a>
                                <a class="dropdown-item" href="#">Last week</a>
                                <a class="dropdown-item" href="#">Last month</a>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="box-body">
                    <div id="revenue5"></div>
                    <div class="d-flex justify-content-center">
                        <p class="d-flex align-items-center fw-600 mx-20"><span
                                class="badge badge-xl badge-dot badge-warning me-20"></span> Progress
                        </p>
                        <p class="d-flex align-items-center fw-600 mx-20"><span
                                class="badge badge-xl badge-dot badge-primary me-20"></span> Done</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-12 mb-4">
            <div class="box">
                <div class="box-body">
                    <p class="text-fade">Total Courses</p>
                    <h3 class="mt-0 mb-20">19 <small class="text-success"><i class="fa fa-arrow-up ms-15 me-5"></i> 2
                            New</small></h3>
                    <div style="min-height: 198px;">
                        <div id="charts_widget_2_chart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-12 mb-4">
            <div class="box">
                <div class="box-body">
                    <p class="text-fade">Total Courses</p>
                    <h3 class="mt-0 mb-20">19 <small class="text-success"><i class="fa fa-arrow-up ms-15 me-5"></i> 2
                            New</small></h3>
                    <div style="min-height: 198px;">
                        <div id="charts_widget_2_chart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-12 mb-4">
            <div class="box">
                <div class="box-body">
                    <p class="text-fade">Total Courses</p>
                    <h3 class="mt-0 mb-20">19 <small class="text-success"><i class="fa fa-arrow-up ms-15 me-5"></i> 2
                            New</small></h3>
                    <div style="min-height: 198px;">
                        <div id="charts_widget_2_chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
