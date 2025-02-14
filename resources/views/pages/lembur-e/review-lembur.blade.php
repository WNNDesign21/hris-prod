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
                        <h4 class="box-title">Review Lembur</h4>
                    </div>
                    <div class="gap-1">
                        <button class="btn btn-success"><i class="fas fa-check-square"></i>&nbsp;&nbsp;Accept</button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info waves-effect btnReload"><i
                                    class="fas fa-sync-alt"></i></button>
                            <button type="button" class="btn btn-warning waves-effect btnFilter"><i
                                    class="fas fa-filter"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="review-table" class="table table-striped table-bordered display nowrap"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center"><input type="checkbox"
                                            style="opacity: 1!important; position:relative!important; left:0px!important;"
                                            id="select-all"></th>
                                    <th>Tanggal</th>
                                    <th>Departemen</th>
                                    <th>Status</th>
                                    <th>Total Durasi</th>
                                    <th>Total Nominal</th>
                                    <th>Total Karyawan</th>
                                    <th>Total Dokumen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pages.lembur-e.modal-detail-review-lembur')
@endsection
