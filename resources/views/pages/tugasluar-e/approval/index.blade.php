@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-tugasluare')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Tugas Luar - Approval</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info waves-effect btnReload"><i
                                    class="fas fa-sync-alt"></i></button>
                            <button type="button" class="btn btn-warning waves-effect btnFilter">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="approval-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID TL</th>
                                    <th>Karyawan</th>
                                    <th>Tanggal</th>
                                    <th>Kendaraan</th>
                                    <th>Pergi</th>
                                    <th>Kembali</th>
                                    <th>Rute</th>
                                    <th>Pengikut</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Checked</th>
                                    <th>Legalized</th>
                                    <th>Known</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pages.tugasluar-e.approval.modal-filter')
    @include('pages.tugasluar-e.approval.modal-reject')
    @include('pages.tugasluar-e.approval.modal-verification')
@endsection
