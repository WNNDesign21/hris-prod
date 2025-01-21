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
    <div class="box">
        <div class="row g-0 py-2">
            <div class="col-12 col-lg-3">
                <div class="box-body be-1 border-light">
                    <div class="flexbox mb-1">
                        <span>
                            <span class="icon-User fs-40"><span class="path1"></span><span class="path2"></span></span><br>
                            HADIR
                        </span>
                        <span class="text-primary fs-40">{{ $hadir . '/' . $total_karyawan }}</span>
                    </div>
                    <div class="progress progress-xxs mt-10 mb-0">
                        <div class="progress-bar" role="progressbar"
                            style="width: {{ ($hadir / $total_karyawan) * 100 }}%; height: 4px;"
                            aria-valuenow="{{ $total_karyawan / $hadir }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3 hidden-down">
                <div class="box-body be-1 border-light">
                    <div class="flexbox mb-1">
                        <span>
                            <span class="icon-Bed fs-40"><span class="path1"></span><span class="path2"></span></span><br>
                            SAKIT
                        </span>
                        <span class="text-info fs-40">952</span>
                    </div>
                    <div class="progress progress-xxs mt-10 mb-0">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 55%; height: 4px;"
                            aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3 d-none d-lg-block">
                <div class="box-body be-1 border-light">
                    <div class="flexbox mb-1">
                        <span>
                            <span class="icon-Book fs-40"><span class="path1"></span><span class="path2"></span><span
                                    class="path3"></span></span><br>
                            IZIN
                        </span>
                        <span class="text-warning fs-40">845</span>
                    </div>
                    <div class="progress progress-xxs mt-10 mb-0">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 65%; height: 4px;"
                            aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3 d-none d-lg-block">
                <div class="box-body">
                    <div class="flexbox mb-1">
                        <span>
                            <span class="icon-Direction fs-40"><span class="path1"></span><span
                                    class="path2"></span></span><br>
                            CUTI
                        </span>
                        <span class="text-danger fs-40">158</span>
                    </div>
                    <div class="progress progress-xxs mt-10 mb-0">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 40%; height: 4px;"
                            aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
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
                                        <th colspan="2">{{ 'Tanggal ' . $i }}</th>
                                    @endfor
                                    <th rowspan="2">MENIT KETERLAMBATAN</th>
                                </tr>
                                <tr>
                                    @for ($i = 1; $i <= 31; $i++)
                                        <th>IN</th>
                                        <th>OUT</th>
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
@endsection
