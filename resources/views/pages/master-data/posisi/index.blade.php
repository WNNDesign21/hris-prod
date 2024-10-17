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
                    <h4 class="box-title">Susunan Korporasi</h4>
                </div>
                <div class="box-body">
                    {{-- <div class="myadmin-dd dd" id="nestable"> --}}
                    <div class="dd" id="nestable">
                        <ol class="dd-list">
                            @foreach ($tree as $item)
                                @include('pages.master-data.posisi.list', ['tree' => $item])
                            @endforeach
                        </ol>
                    </div>
                    {{-- <ul>
                            @foreach ($tree as $item)
                                @include('pages.master-data.posisi.list', ['tree' => $item])
                            @endforeach
                        </ul> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <h4 class="box-title">Data Posisi</h4>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info waves-effect btnReload"><i
                                class="fas fa-sync-alt"></i></button>
                        <button type="button" class="btn btn-success waves-effect btnAdd"><i
                                class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="posisi-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Organisasi</th>
                                    <th>Divisi</th>
                                    <th>Departemen</th>
                                    <th>Seksi</th>
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
    @include('pages.master-data.posisi.modal-tambah')
    @include('pages.master-data.posisi.modal-edit')
@endsection
