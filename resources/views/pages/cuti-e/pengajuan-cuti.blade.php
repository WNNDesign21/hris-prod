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
                    <div class="row">
                        <h4 class="box-title">List Data Cuti Personal</h4>
                        <br>
                        <br>
                        <small>Jatah Cuti Pribadi Total ({{ date('Y') }}) : <span class="text-bold"
                                id="sisa_cuti_display">{{ auth()->user()->karyawan->sisa_cuti }} Hari</span>
                            <br> Hutang Cuti ({{ date('Y') }}) : <span class="text-bold"
                                id="hutang_cuti_display">{{ auth()->user()->karyawan->hutang_cuti }} Hari</span></small>
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
                        <table id="personal-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Action</th>
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
                                    {{-- <th>Action</th> --}}
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
