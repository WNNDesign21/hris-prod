@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-lembure')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box mb-0">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Dashboard Lembur</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning waves-effect btnFilterMonthly"><i
                                    class="fas fa-filter"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div id="chartMonthlyLemburPerDepartemen" class="dask evt-cal min-h-400 p-4"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12 mb-4">
            <div class="box mb-0">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Grafik Lembur</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning waves-effect btnFilterCurrent"><i
                                    class="fas fa-filter"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div id="chartCurrentMonthLemburPerDepartemen" class="dask evt-cal min-h-700 p-4"></div>
                </div>
            </div>
        </div>
        <div class="col-12 mb-4">
            <div class="box mb-0">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Weekly Lembur</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning waves-effect btnFilterWeekly"><i
                                    class="fas fa-filter"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div id="chartWeeklyLemburPerDepartemen" class="dask evt-cal min-h-700 p-4"></div>
                </div>
            </div>
        </div>
    </div>
    @include('pages.lembur-e.modal-filter-dashboard-monthly')
    @include('pages.lembur-e.modal-filter-dashboard-current')
    @include('pages.lembur-e.modal-filter-dashboard-weekly')
@endsection
