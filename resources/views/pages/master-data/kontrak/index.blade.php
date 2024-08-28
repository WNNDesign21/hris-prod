@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-masterdata')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <h4 class="box-title">Data Kontrak Karyawan</h4>
                    <div>
                        <button type="button" class="btn btn-info waves-effect btnReload"><i
                                class="fas fa-sync-alt"></i></button>
                        <button type="button" class="btn btn-success waves-effect btnAdd">Tambah Kontrak</button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="kontrak-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID Kontrak</th>
                                    <th>Nama</th>
                                    <th>Posisi</th>
                                    <th>No. Surat</th>
                                    <th>Issued Date</th>
                                    <th>Jenis Kontrak</th>
                                    <th>Status</th>
                                    <th>Durasi</th>
                                    <th>Salary</th>
                                    <th>Status Changed</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th class="text-center">Attachment</th>
                                    <th class="text-center">Evidence</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pages.master-data.kontrak.modal-tambah')
    @include('pages.master-data.kontrak.modal-edit')
@endsection
