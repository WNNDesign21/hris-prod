@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-ksk')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Tindak Lanjut KSK</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info waves-effect btnReload"><i
                                    class="fas fa-sync-alt"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-pills mb-20">
                        <li class="nav-item"> <a href="#need-action" class="nav-link active" data-bs-toggle="tab"
                                aria-expanded="false">Need Action</a> </li>
                        <li class="nav-item"> <a href="#history" class="nav-link" data-bs-toggle="tab"
                                aria-expanded="false">History</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="need-action" class="tab-pane active">
                            <div class="table-responsive">
                                <table id="need-action-table" class="table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID KSK</th>
                                            <th>Karyawan</th>
                                            <th>Departemen</th>
                                            <th>Jabatan</th>
                                            <th>Posisi</th>
                                            <th>Tanggal Akhir Bekerja</th>
                                            <th>Status KSK</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div id="history" class="tab-pane">
                            <table id="history-table" class="table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID KSK</th>
                                        <th>Karyawan</th>
                                        <th>Departemen</th>
                                        <th>Jabatan</th>
                                        <th>Posisi</th>
                                        <th>Tanggal Akhir Bekerja</th>
                                        <th>Status KSK</th>
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
    @include('pages.ksk-e.tindak-lanjut.modal-detail')
    @include('pages.ksk-e.tindak-lanjut.modal-kontrak')
    @include('pages.ksk-e.tindak-lanjut.modal-turnover')
@endsection
