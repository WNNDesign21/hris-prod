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
                <div class="box-body">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-input-pengajuan-cuti">
                        @csrf
                        <div class="row p-4">
                            <div class="col-lg-6 col-12">
                                <h5>Form Pengajuan Cuti</h5>
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
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
