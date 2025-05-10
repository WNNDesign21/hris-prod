@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-izine')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">List Approval SKD</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info waves-effect btnReload"><i
                                    class="fas fa-sync-alt"></i></button>
                            <button type="button" class="btn btn-warning waves-effect btnFilter"><i
                                    class="fas fa-filter"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-pills mb-20">
                        <li class="nav-item"> <a href="#must-approved" class="nav-link active" data-bs-toggle="tab"
                                aria-expanded="false">Need Approved</a> </li>
                        <li class="nav-item"> <a href="#alldata" class="nav-link" data-bs-toggle="tab"
                                aria-expanded="false">All Data</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="must-approved" class="tab-pane active">
                            <div class="table-responsive">
                                <table id="must-approved-table" class="table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Departemen</th>
                                            <th>Posisi</th>
                                            <th>Created At</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Durasi</th>
                                            <th>Keterangan</th>
                                            <th>Lampiran</th>
                                            <th>Approved</th>
                                            <th>Legalized</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div id="alldata" class="tab-pane">
                            <div class="table-responsive">
                                <table id="alldata-table" class="table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Departemen</th>
                                            <th>Posisi</th>
                                            <th>Created At</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Durasi</th>
                                            <th>Keterangan</th>
                                            <th>Lampiran</th>
                                            <th>Approved</th>
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
        </div>
    </div>
    @include('pages.izin-e.modal-reject-skd')
    @include('pages.izin-e.modal-filter-skd')
@endsection
