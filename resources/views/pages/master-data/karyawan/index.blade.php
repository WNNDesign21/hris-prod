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
                    <h4 class="box-title">Data Karyawan</h4>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info waves-effect btnReload"><i
                                class="fas fa-sync-alt"></i></button>
                        <button type="button" class="btn btn-warning waves-effect btnFilter"><i
                                class="fas fa-filter"></i></button>
                        <button type="button" class="btn btn-success waves-effect btnAdd"><i
                                class="fas fa-plus"></i></button>
                        <button type="button" class="btn btn-dark waves-effect btnUpload"><i
                                class="fas fa-upload"></i></button>
                        <button type="button" class="btn btn-light waves-effect btnTemplate"><i
                                class="fas fa-file-excel"></i></button>
                    </div>
                    <input type="file" id="upload-karyawan" class="d-none">
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="karyawan-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID Karyawan</th>
                                    <th>Nama</th>
                                    <th>Departemen</th>
                                    <th>Posisi</th>
                                    <th>Grup</th>
                                    <th>Jenis Kontrak</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status Karyawan</th>
                                    <th>NIK KTP</th>
                                    <th>No. KK</th>
                                    <th>Tempat Lahir</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Agama</th>
                                    <th>Alamat</th>
                                    <th>Domisili</th>
                                    <th>No. NPWP</th>
                                    <th>No. BPJS Kesehatan</th>
                                    <th>No. BPJS Ketenagakerjaan</th>
                                    <th>No. HP</th>
                                    <th>Email</th>
                                    <th>Nama Bank</th>
                                    <th>No. Rekening</th>
                                    <th>Atas Nama Rekening</th>
                                    <th>Nama Ibu Kandung</th>
                                    <th>Pendidikan Terakhir</th>
                                    <th>Jurusan</th>
                                    <th>Nomor Darurat</th>
                                    <th>Golongan Darah</th>
                                    <th>Jatah Cuti</th>
                                    <th>Hutang Cuti</th>
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
    @include('pages.master-data.karyawan.modal-tambah')
    @include('pages.master-data.karyawan.modal-edit')
    @include('pages.master-data.karyawan.modal-akun')
    @include('pages.master-data.karyawan.modal-kontrak')
    @include('pages.master-data.karyawan.modal-filter')
@endsection
