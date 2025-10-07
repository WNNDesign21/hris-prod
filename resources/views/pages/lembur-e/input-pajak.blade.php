@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-lembure')
@endsection

@section('content')
    {{-- Box for Upload --}}
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <h4 class="box-title">Input Pajak (PPH) Lembur via Excel</h4>
                    <a href="{{ route('lembure.input-pajak-lembur.download-template') }}" class="btn btn-primary">
                        <i class="fa fa-download"></i> Download Template
                    </a>
                </div>
                <div class="box-body">
                    <form id="form-upload-pph" action="{{ route('lembure.input-pajak-lembur.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="periode-upload" class="form-label">Periode Upload <span class="text-danger">*</span></label>
                                    <input type="month" class="form-control" id="periode-upload" name="periode" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Mode Upload <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="upload_mode" id="mode_insert" value="insert" checked>
                                            <label class="form-check-label" for="mode_insert">
                                                Insert (Data Baru)
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="upload_mode" id="mode_update" value="update">
                                            <label class="form-check-label" for="mode_update">
                                                Update (Perbarui Data)
                                            </label>
                                        </div>
                                    </div>
                                    <small class="text-fade">Pilih 'Insert' untuk periode baru, atau 'Update' untuk menimpa data pada periode yang sudah ada.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="file_pph" class="form-label">File Excel <span class="text-danger">*</span></label>
                                    <input class="form-control" type="file" id="file_pph" name="file_pph" required accept=".xlsx, .xls">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-upload"></i> Upload Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Box for Datatable --}}
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header">
                    <h4 class="box-title">Data PPH Lembur</h4>
                </div>
                <div class="box-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="periode-filter" class="form-label">Periode</label>
                            <input type="month" class="form-control" id="periode-filter">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="filter-btn" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="pph-lembur-table" class="table table-bordered table-striped" style="width:100%"
                            data-datatable-url="{{ route('lembure.input-pajak-lembur.datatable') }}"
                            data-update-url-template="{{ route('lembure.input-pajak-lembur.update', ':id') }}"
                            data-destroy-url-template="{{ route('lembure.input-pajak-lembur.destroy', ':id') }}"
                            data-csrf-token="{{ csrf_token() }}"
                        >
                            <thead>
                                <tr>
                                    <th>NIK</th>
                                    <th>Nama Karyawan</th>
                                    <th>Potongan PPH</th>
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
@endsection

@push('scripts')
@vite('resources/js/pages/lembure-input-pajak.js')
@endpush