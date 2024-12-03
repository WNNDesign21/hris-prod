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
                        <h4 class="box-title">List Pengajuan Izin</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info waves-effect btnReload"><i
                                    class="fas fa-sync-alt"></i></button>
                            <button type="button" class="btn btn-success waves-effect btnAdd"><i
                                    class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="pengajuan-izin-table" class="table table-striped table-bordered display"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID Izin</th>
                                    <th>Rencana Mulai / Masuk</th>
                                    <th>Rencana Selesai / Keluar</th>
                                    <th>Aktual Mulai / Masuk</th>
                                    <th>Aktual Selesai / Keluar</th>
                                    <th>Jenis Izin</th>
                                    <th>Durasi</th>
                                    <th>Keterangan</th>
                                    <th>Checked</th>
                                    <th>Approved</th>
                                    <th>Legalized</th>
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
    @include('pages.izin-e.modal-pengajuan-izin')
    @include('pages.izin-e.modal-edit-pengajuan-izin')
@endsection
