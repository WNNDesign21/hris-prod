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
                    <h4 class="box-title">List Event<br><small>Note : Ketika menginput event dengan
                            jenis cuti bersama, jatah
                            cuti
                            bersama seluruh karyawan akan otomatis terpotong dan berkurang,<br> jika karyawan belum memiliki
                            jatah cuti bersama, maka akan masuk kedalam hutang cuti yang harus dibayar pada tahun
                            berikutnya <br> (Cuti bersama akan muncul pada kalender cuti dashboard)</small></h4>
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
                        <table id="event-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Jenis Event</th>
                                    <th>Keterangan</th>
                                    <th>Durasi</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
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
    @include('pages.master-data.event.modal-input')
@endsection
