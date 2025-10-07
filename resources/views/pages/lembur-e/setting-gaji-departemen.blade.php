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
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Setting Gaji Departemen</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info waves-effect btnReload"><i
                                    class="fas fa-sync-alt"></i></button>
                            <button type="button" class="btn btn-success waves-effect btnAdd"><i
                                    class="fas fa-plus"></i></button>
                            <button type="button" class="btn btn-warning waves-effect btnFilter">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="setting-gaji-departemen-table" class="table table-striped table-bordered display"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Departemen</th>
                                    <th>Periode</th>
                                    <th>Batas Nominal Lembur</th>
                                    <th>Presentase (%)</th>
                                    <th>Total Gaji</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pages.lembur-e.modal-tambah-gaji-departemen')
    @include('pages.lembur-e.modal-filter-setting-gaji-departemen')
@endsection
