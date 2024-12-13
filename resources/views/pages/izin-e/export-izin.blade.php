@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-izine')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box no-shadow">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Export Report Izin & SKD</h4>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <div class="box">
                                <div class="box-header d-flex justify-content-between">
                                    <div class="row">
                                        <h4 class="box-title">Rekap Izin & SKD</h4>
                                    </div>
                                </div>
                                <div class="box-body min-h-200">
                                    <form action="#" method="POST" enctype="multipart/form-data"
                                        id="form-export-rekap-izin-dan-skd">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="export_data">Data</label>
                                                    <select class="form-control" name="export_data" id="export_data">
                                                        <option value="">IZIN & SKD</option>
                                                        <option value="IZIN">IZIN</option>
                                                        <option value="SKD">SKD</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="departemen">Departemen</label>
                                                    <select class="form-control" name="departemen" id="departemen">
                                                        <option value="">SEMUA DEPARTEMEN</option>
                                                        @foreach ($departments as $item)
                                                            <option value="{{ $item->id_departemen }}">{{ $item->nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="periode">Periode</label>
                                                    <input type="month" class="form-control" name="periode"
                                                        id="periode">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12 p-4 d-flex justify-content-end">
                                                <button type="button" class="btn btn-success" id="btnExport"><i
                                                        class="fas fa-file-export"></i>
                                                    Export</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
