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
        <div class="col-lg-6 col-12">
            <div class="box">
                <div class="box-body">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-input-pengajuan-cuti">
                        @csrf
                        <div class="row p-4">
                            <div class="col-12">
                                <h5>Form Pengajuan Cuti</h5>
                                <hr>
                                <div class="form-group">
                                    <label for="">Jenis Cuti<span class="text-danger">*</span></label>
                                    <select name="jenis_cuti" id="jenis_cuti" class="form-control" style="width: 100%;">
                                        <option value="PRIBADI">PRIBADI</option>
                                        <option value="SAKIT">SAKIT</option>
                                        <option value="KHUSUS">KHUSUS</option>
                                    </select>
                                </div>
                                <div class="form-group" id="conditional_field">
                                </div>
                                <div class="form-group">
                                    <label for="">Rencana Mulai</label>
                                    <input type="date" name="rencana_mulai_cuti" id="rencana_mulai_cuti"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Rencana Selesai</label>
                                    <input type="date" name="rencana_selesai_cuti" id="rencana_selesai_cuti"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Alasan Cuti</label>
                                    <textarea class="form-control" name="alasan_cuti" id="alasan_cuti" style="width: 100%;"></textarea>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                        Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <h4 class="box-title">List Data Cuti Personal</h4>
                    <div>
                        <button type="button" class="btn btn-info waves-effect btnReload"><i
                                class="fas fa-sync-alt"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="cutie-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th colspan="2">Rencana</th>
                                    <th colspan="2">Aktual</th>
                                    <th rowspan="2">Durasi</th>
                                    <th rowspan="2">Jenis</th>
                                    <th rowspan="2">Alasan</th>
                                    <th rowspan="2">Karyawan Pengganti</th>
                                    <th colspan="3">Document Progress</th>
                                    <th rowspan="2">Status Dokumen</th>
                                    <th rowspan="2">Status</th>
                                    <th rowspan="2">Attachment</th>
                                    <th rowspan="2">Action</th>
                                </tr>
                                <tr>
                                    <th>Mulai</th>
                                    <th>Selesai</th>
                                    <th>Mulai</th>
                                    <th>Selesai</th>
                                    <th>Checked</th>
                                    <th>Approved</th>
                                    <th>Legalize</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
