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
                    <h4 class="box-title">Turnover Karyawan</h4>
                </div>
                <div class="box-body">
                    <form action="{{ route('master-data.turnover.store') }}" method="POST" enctype="multipart/form-data"
                        id="form-input-turnover">
                        @csrf
                        <div class="row p-4">
                            <div class="col-lg-4 col-12">
                                <h5>Form Turnover Karyawan</h5>
                                <hr>
                                <div class="form-group">
                                    <label for="">Karyawan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="karyawan_id" name="karyawan_id" style="width: 100%;"
                                        required>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Status Karyawan <span class="text-danger">*</span></label>
                                    <select name="status_karyawan" id="status_karyawan" class="form-control"
                                        style="width: 100%;">
                                        <option value="RESIGN">RESIGN</option>
                                        <option value="TERMINASI">TERMINASI</option>
                                        <option value="PENSIUN">PENSIUN</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Keluar</label>
                                    <input type="date" name="tanggal_keluar" id="tanggal_keluar" class="form-control"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <textarea name="keterangan" id="keterangan" class="form-control" required></textarea>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                        Submit</button>
                                </div>
                            </div>
                            <div class="col-lg-8 col-12">
                                <h5>Riwayat Turnover</h5>
                                <hr>
                                <div class="table-responsive">
                                    <table id="turnover-table" class="table table-striped table-bordered display"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID Turnover</th>
                                                <th>ID Karyawan</th>
                                                <th>Nama</th>
                                                <th>Status</th>
                                                <th>Tanggal Keluar</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
