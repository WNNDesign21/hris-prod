@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-attendance')
@endsection

@section('content')
    <div class="row mb-2">
        <div class="d-flex justify-content-end">
            <div class="btn-group">
                <button type="button" class="btn btn-warning waves-effect btnFilterSummary"><i
                        class="fas fa-filter"></i></button>
            </div>
        </div>
    </div>
    <div class="row" id="summaryContent">
        <div class="col-6 col-md-4 col-xl-3">
            <a class="box box-link-shadow text-center btnDetailSummary" href="javascript:void(0)" style="cursor: pointer;"
                data-type="1">
                <div class="box-body py-25 bg-primary bbsr-0 bber-0">
                    <p class="fw-600 fs-24">HADIR</p>
                </div>
                <div class="box-body">
                    <h3><span class="text-primary fs-40 hadirText">{{ $hadir . '/' . $total_karyawan }}</span></h3>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <a class="box box-link-shadow text-center btnDetailSummary" href="javascript:void(0)" style="cursor: pointer;"
                data-type="2">
                <div class="box-body py-25 bg-danger bbsr-0 bber-0">
                    <p class="fw-600 fs-24">SAKIT</p>
                </div>
                <div class="box-body">
                    <h3><span class="text-danger fs-40 sakitText">{{ $sakit . '/' . $total_karyawan }}</span></h3>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <a class="box box-link-shadow text-center btnDetailSummary" href="javascript:void(0)" style="cursor: pointer;"
                data-type="3">
                <div class="box-body py-25 bg-info bbsr-0 bber-0">
                    <p class="fw-600 fs-24">IZIN</p>
                </div>
                <div class="box-body">
                    <h3><span class="text-info fs-40 izinText">{{ $izin . '/' . $total_karyawan }}</span></h3>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <a class="box box-link-shadow text-center btnDetailSummary" href="javascript:void(0)" style="cursor: pointer;"
                data-type="4">
                <div class="box-body py-25 bg-success bbsr-0 bber-0">
                    <p class="fw-600 fs-24">CUTI</p>
                </div>
                <div class="box-body">
                    <h3><span class="text-success fs-40 cutiText">{{ $cuti . '/' . $total_karyawan }}</span></h3>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Presensi Karyawan</h4>
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
                        <table id="presensi-table" class="table table-striped table-bordered display nowrap"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th rowspan="2">NIK</th>
                                    <th rowspan="2">NAMA</th>
                                    <th rowspan="2">DEPARTEMEN</th>
                                    <th rowspan="2">PIN</th>
                                    @for ($i = 1; $i <= 31; $i++)
                                        @if ($i == date('j'))
                                            <th colspan="2" class="bg-primary">{{ 'Tanggal ' . $i }}</th>
                                        @else
                                            <th colspan="2">{{ 'Tanggal ' . $i }}</th>
                                        @endif
                                    @endfor
                                    <th rowspan="2">MENIT KETERLAMBATAN</th>
                                    <th rowspan="2">TOTAL KEHADIRAN</th>
                                </tr>
                                <tr>
                                    @for ($i = 1; $i <= 31; $i++)
                                        @if ($i == date('j'))
                                            <th class="bg-primary">IN - {{ $i }}</th>
                                            <th class="bg-primary">OUT- {{ $i }}</th>
                                        @else
                                            <th>IN - {{ $i }}</th>
                                            <th>OUT- {{ $i }}</th>
                                        @endif
                                    @endfor
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pages.attendance-e.presensi.modal-filter')
    @include('pages.attendance-e.presensi.modal-filter-summary')
    @include('pages.attendance-e.presensi.modal-detail-summary')
    @include('pages.attendance-e.presensi.modal-check')
@endsection
