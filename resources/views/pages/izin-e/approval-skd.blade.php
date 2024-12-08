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
                    <div class="table-responsive">
                        <table id="approval-skd-table" class="table table-striped table-bordered display"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Departemen</th>
                                    <th>Posisi</th>
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
    @include('pages.izin-e.modal-reject-skd')
    @include('pages.izin-e.modal-filter-skd')
@endsection
