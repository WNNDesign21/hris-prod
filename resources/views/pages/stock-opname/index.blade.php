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
                        <h4 class="box-title">Data STO</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            {{-- BUTTON RELOAD TABEL STO --}}
                            <button type="button" class="btn btn-info waves-effect btnReload"><i
                                    class="fas fa-sync-alt"></i></button>
                            {{-- BUTTON TAMBAH STO --}}
                            <button type="button" class="btn btn-success waves-effect btnAdd"><i
                                    class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="sto-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    {{-- MASUKIN TD YANG AKAN DI DISPLAY DISINI --}}
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL INCLUDE DISINI --}}
    {{-- @include('pages.cuti-e.modal-pengajuan-cuti') --}}
@endsection
