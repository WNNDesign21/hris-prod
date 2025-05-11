@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-cutie')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <h4 class="box-title">Approval Cuti</h4>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info waves-effect btnReload"><i
                                class="fas fa-sync-alt"></i></button>
                        <button type="button" class="btn btn-warning waves-effect btnFilter"><i
                                class="fas fa-filter"></i></button>
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
                                            <th>Rencana Mulai</th>
                                            <th>Rencana Selesai</th>
                                            <th>Durasi</th>
                                            <th>Jenis</th>
                                            <th>Checked 1</th>
                                            <th>Checked 2</th>
                                            <th>Approved</th>
                                            <th>Legalized</th>
                                            <th>Status Dokumen</th>
                                            <th>Status</th>
                                            <th>Alasan</th>
                                            <th>Karyawan Pengganti</th>
                                            <th>Created At</th>
                                            <th>Action</th>
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
                                            <th>Rencana Mulai</th>
                                            <th>Rencana Selesai</th>
                                            <th>Durasi</th>
                                            <th>Jenis</th>
                                            <th>Checked 1</th>
                                            <th>Checked 2</th>
                                            <th>Approved</th>
                                            <th>Legalized</th>
                                            <th>Status Dokumen</th>
                                            <th>Status</th>
                                            <th>Alasan</th>
                                            <th>Karyawan Pengganti</th>
                                            <th>Created At</th>
                                            <th>Action</th>
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
    @include('pages.cuti-e.modal-reject-cuti')
    @include('pages.cuti-e.modal-karyawan-pengganti-cuti')
    @include('pages.cuti-e.modal-filter')
@endsection
