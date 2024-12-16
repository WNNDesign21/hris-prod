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
            <div class="box">
                <div class="box-body d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Detail Lembur</h4>
                        <h3>
                            Filter Status : <span id="filterStatus">-</span></h3>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info waves-effect btnReload"><i
                                    class="fas fa-sync-alt"></i></button>
                            <button type="button" class="btn btn-warning waves-effect btnFilter">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12 mb-4">
            <div class="box mb-0">
                <div class="box-body">
                    <div id="chartLeaderboardUserMonthly" class="dask evt-cal min-h-1000 p-4"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="detail-lembur-table" class="table table-striped table-bordered display nowrap"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID Lembur</th>
                                    <th>Nama</th>
                                    <th>Posisi</th>
                                    <th>Departemen</th>
                                    <th>Mulai</th>
                                    <th>Selesai</th>
                                    <th>Durasi</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pages.lembur-e.modal-filter-detail-lembur')
@endsection
