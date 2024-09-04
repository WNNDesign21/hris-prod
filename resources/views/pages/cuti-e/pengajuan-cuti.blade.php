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
                    <h4 class="box-title">List Data Cuti Personal</h4>
                    <div>
                        <button type="button" class="btn btn-info waves-effect btnReload"><i
                                class="fas fa-sync-alt"></i></button>
                        <button type="button" class="btn btn-success waves-effect btnAdd">Ajukan Cuti</button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="cutie-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Rencana Mulai</th>
                                    <th>Rencana Selesai</th>
                                    <th>Aktual Mulai</th>
                                    <th>Aktual Selesai</th>
                                    <th>Durasi</th>
                                    <th>Jenis</th>
                                    <th>Alasan</th>
                                    <th>Karyawan Pengganti</th>
                                    <th>Checked</th>
                                    <th>Approved</th>
                                    <th>Legalized</th>
                                    <th>Status Dokumen</th>
                                    <th>Status</th>
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
    @include('pages.cuti-e.modal-pengajuan-cuti')
@endsection
