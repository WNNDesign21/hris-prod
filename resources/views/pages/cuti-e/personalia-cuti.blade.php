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
                    <h4 class="box-title">List Data Cuti</h4>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info waves-effect btnReload"><i
                                class="fas fa-sync-alt"></i></button>
                        <button type="button" class="btn btn-warning waves-effect btnFilter"><i
                                class="fas fa-filter"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="personalia-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Departemen</th>
                                    <th>Rencana Mulai</th>
                                    <th>Rencana Selesai</th>
                                    <th>Aktual Mulai</th>
                                    <th>Aktual Selesai</th>
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
                                    <th>Attachment</th>
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
    @include('pages.cuti-e.modal-reject-cuti')
    @include('pages.cuti-e.modal-filter')
@endsection
