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
                    <div class="row">
                        <h4 class="box-title">Template Surat</h4>
                        <br>
                        <br>
                        <small style="width: 70%;">
                            <strong>Note</strong> : Template bisa customisasi sesuai kebutuhan, untuk membuat template
                            sesuai keinginan,
                            silahkan sisipkan variabel ($no_surat, $day, $nama dll) pada dokumen yang hendak dijadikan
                            sebagai template surat. <br>
                            template surat bisa di download <a href="{{ asset('template/kontrak_pkwt_karawang.docx') }}"
                                class="text-primary fw-bold">disini (Template)</a> dan hasil akhirnya akan menjadi seperti <a
                                href="{{ asset('template/kontrak_pkwt_karawang_result.docx') }}"
                                class="text-primary fw-bold">disini (Contoh Hasil Template)</a>. <br><br>
                            Untuk mengaktifkan template, silahkan ubah status <strong>isActive</strong> pada template
                            menjadi <strong>AKTIF</strong>
                        </small>
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
                        <table id="template-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nama Template</th>
                                    <th>Type</th>
                                    <th>IsActive</th>
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
    @include('pages.master-data.template.modal-tambah')
    @include('pages.master-data.template.modal-edit')
@endsection
