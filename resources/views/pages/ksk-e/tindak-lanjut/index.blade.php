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
                    <div class="table-responsive">
                        <table id="tindak-lanjut-table" class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID Cleareance</th>
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
            </div>
        </div>
    </div>
    @include('pages.ksk-e.tindak-lanjut.modal-kontrak')
    @include('pages.ksk-e.tindak-lanjut.modal-turnover')
@endsection
