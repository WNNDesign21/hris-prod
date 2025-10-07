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
    <div class="row">
        <div class="col-12">
            <div class="box no-shadow">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Export Report Lembur</h4>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <div class="box">
                                <div class="box-header d-flex justify-content-between">
                                    <div class="row">
                                        <h4 class="box-title">Rekap Lembur</h4>
                                    </div>
                                </div>
                                <div class="box-body min-h-200">
                                    <form action="{{ route('lembure.export-report-lembur.rekap-lembur-perbulan') }}"
                                        method="POST" enctype="multipart/form-data" id="form-export-report-lembur">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="periode_rekap">Periode</label>
                                                    <input type="month" class="form-control" name="periode_rekap"
                                                        id="periode_rekap">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12 p-4 d-flex justify-content-end">
                                                <button type="submit" class="btn btn-success"><i
                                                        class="fas fa-file-export"></i>
                                                    Rekap</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="box">
                                <div class="box-header d-flex justify-content-between">
                                    <div class="row">
                                        <h4 class="box-title">Slip Lembur</h4>
                                    </div>
                                </div>
                                <div class="box-body min-h-200">
                                    <form action="{{ route('lembure.export-report-lembur.export-slip-lembur-perbulan') }}"
                                        method="POST" enctype="multipart/form-data" id="form-export-slip-lembur">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="periode_slip">Periode</label>
                                                    <input type="month" class="form-control" name="periode_slip"
                                                        id="periode_slip">
                                                </div>
                                                <div class="form-group">
                                                    <label for="departemen">Departemen</label>
                                                    <select class="form-control" name="departemen_slip" id="departemen_slip"
                                                        style="width: 100%;">
                                                        <option value="">ALL DEPARTMENT</option>
                                                        @foreach ($departments as $item)
                                                            <option value="{{ $item->id_departemen }}">{{ $item->nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12 p-4 d-flex justify-content-end">
                                                <button type="submit" class="btn btn-success"><i
                                                        class="fas fa-file-export"></i>
                                                    Export</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="box">
                            <div class="box-header d-flex justify-content-between">
                                <div class="row">
                                    <h4 class="box-title">List Export Slip Lembur</h4>
                                </div>
                                <div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info waves-effect btnReload"><i
                                                class="fas fa-sync-alt"></i></button>
                                        <button type="button" class="btn btn-warning waves-effect btnFilter"><i
                                                class="fas fa-filter"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="export-slip-lembur-table" class="table table-striped table-bordered display"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Departemen</th>
                                                <th>Created At</th>
                                                <th>Periode</th>
                                                <th>Status</th>
                                                <th>Message</th>
                                                <th>Attachment</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pages.lembur-e.modal-filter-export-report-lembur')
@endsection
