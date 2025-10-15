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
    {{-- DASHBOARD MASTER DATA KARYAWAN --}}
    <div class="row">
        <div class="col-12">
            <div class="box no-shadow mb-0 bg-transparent">
                <div class="box-header no-border px-0">
                    <h4 class="box-title">Dashboard Master Data Karyawan</h4>
                </div>
            </div>
        </div>

        {{-- DATA KARYAWAN --}}
        <div class="col-xl-4 col-12">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Data Karyawan <br>
                        <small>{{ \Carbon\Carbon::now()->format('F Y') }}</small>
                    </h4>
                </div>
                <div class="box-body px-0 pt-0">
                    <div class="media-list media-list-hover">
                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="aktif_karyawan">
                                <i class="fas fa-sync-alt fa-spin fs-24"></i>
                            </h4>
                            <div class="media-body ps-15 bs-5 rounded border-success">
                                <h5>AKTIF</h5>
                            </div>
                        </a>
                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="habis_kontrak_karyawan">
                                <i class="fas fa-sync-alt fa-spin fs-24"></i>
                            </h4>
                            <div class="media-body ps-15 bs-5 rounded border-primary">
                                <h5>HABIS KONTRAK</h5>
                            </div>
                        </a>
                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="mengundurkan_diri_karyawan">
                                <i class="fas fa-sync-alt fa-spin fs-24"></i>
                            </h4>
                            <div class="media-body ps-15 bs-5 rounded border-danger">
                                <h5>MENGUNDURKAN DIRI</h5>
                            </div>
                        </a>
                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="pensiun_karyawan">
                                <i class="fas fa-sync-alt fa-spin fs-24"></i>
                            </h4>
                            <div class="media-body ps-15 bs-5 rounded border-info">
                                <h5>PENSIUN</h5>
                            </div>
                        </a>
                        <a class="media media-single" href="#">
                            <h4 class="w-20 text-gray fw-500" id="terminasi_karyawan">
                                <i class="fas fa-sync-alt fa-spin fs-24"></i>
                            </h4>
                            <div class="media-body ps-15 bs-5 rounded border-warning">
                                <h5>TERMINASI</h5>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- TURNOVER --}}
        <div class="col-xl-8 col-12">
            <div class="box">
                <div class="box-body">
                    <h3 class="mt-0 mb-5">Turnover Karyawan {{ date('Y') }}</h3>
                    <p class="text-fade">Total Karyawan Keluar : {{ $jumlah_karyawan_keluar }}</p>
                    <div style="min-height: 245px;">
                        <div id="turnover-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRAFIK TAMBAHAN --}}
    <div class="row">
        <div class="col-xl-6 col-12">
            <div class="box">
                <div class="box-body">
                    <h3 class="mt-0 mb-5">Turnover Detail {{ date('Y') }}</h3>
                    <div style="min-height: 198px;">
                        <div id="turnover-detail-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-12">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Kontrak Progress</h4>
                </div>
                <div class="box-body">
                    <div style="min-height: 198px;">
                        <div id="kontrak-progress-chart"></div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <p class="d-flex align-items-center fw-600 mx-20">
                            <span class="badge badge-xl badge-dot badge-warning me-20"></span> On Progress
                        </p>
                        <p class="d-flex align-items-center fw-600 mx-20">
                            <span class="badge badge-xl badge-dot badge-primary me-20"></span> Done
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-12 mb-4">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">
                        Total Data by Status <br>
                        <small>2017 - {{ date('Y') }}</small>
                    </h4>
                </div>
                <div class="box-body">
                    <div style="min-height: 198px;">
                        <div id="total-data-by-status-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row mt-4">
        {{-- ===================== DESA & KECAMATAN ===================== --}}
        <div class="col-xl-6 col-12">
            <div class="box">
                <div class="box-header with-border d-flex justify-content-between align-items-center">
                    <h4 class="box-title">Rekap Desa</h4>
                    <div>
                        <select id="filter-desa" class="form-select form-select-sm">
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row align-items-center">
                        <div class="col-lg-7 col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-desa">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Desa/Kelurahan</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel-rekap-desa">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Memuat data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-5 col-12 text-center">

                            <div id="chart-desa" style="height:250px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-12">
            <div class="box">
                <div class="box-header with-border d-flex justify-content-between align-items-center">
                    <h4 class="box-title">Rekap Kecamatan</h4>
                    <div>
                        <select id="filter-kecamatan" class="form-select form-select-sm">
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row align-items-center">
                        <div class="col-lg-7 col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-kecamatan">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Kecamatan</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel-rekap-kecamatan">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Memuat data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-5 col-12 text-center">

                            <div id="chart-kecamatan" style="height:250px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        {{-- ===================== KABUPATEN & PROVINSI ===================== --}}
        <div class="col-xl-6 col-12">
            <div class="box">
                <div class="box-header with-border d-flex justify-content-between align-items-center">
                    <h4 class="box-title">Rekap Kabupaten</h4>
                    <div>
                        <select id="filter-kabupaten" class="form-select form-select-sm">
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row align-items-center">
                        <div class="col-lg-7 col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-kabupaten">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Kabupaten</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel-rekap-kabupaten">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Memuat data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-5 col-12 text-center">

                            <div id="chart-kabupaten" style="height:250px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-12">
            <div class="box">
                <div class="box-header with-border d-flex justify-content-between align-items-center">
                    <h4 class="box-title">Rekap Provinsi</h4>
                    <div>
                        <select id="filter-provinsi" class="form-select form-select-sm">
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row align-items-center">
                        <div class="col-lg-7 col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-provinsi">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Provinsi</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel-rekap-provinsi">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Memuat data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-5 col-12 text-center">

                            <div id="chart-provinsi" style="height:250px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- REKAP WILAYAH KARAWANG --}}
    {{-- ===================== REKAP DALAM & LUAR KARAWANG + REKAP KONTRAK ===================== --}}
    <div class="row mt-4">
        {{-- REKAP DALAM & LUAR KARAWANG --}}
        <div class="col-xl-6 col-12">
            <div class="box">
                <div class="box-header with-border d-flex justify-content-between align-items-center">
                    <h4 class="box-title">Rekap Dalam & Luar Karawang</h4>
                    <div>
                        <select id="filter-karawang" class="form-select form-select-sm">
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row align-items-center">
                        <div class="col-lg-7 col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-karawang">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Keterangan</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel-rekap-karawang">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Memuat data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-5 col-12 text-center">
                            <div id="chart-karawang" style="height:250px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- REKAP KARYAWAN BERDASARKAN KONTRAK --}}
        <div class="col-xl-6 col-12">
            <div class="box">
                <div class="box-header with-border d-flex justify-content-between align-items-center">
                    <h4 class="box-title">Rekap Karyawan Berdasarkan Kontrak</h4>
                    <div>
                        <select id="filter-kontrak" class="form-select form-select-sm">
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row align-items-center">
                        <div class="col-lg-7 col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-kontrak">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Keterangan</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel-rekap-kontrak">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Memuat data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-5 col-12 text-center">
                            <div id="chart-kontrak" style="height:250px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-xl-6 col-12">
            <div class="box">
                <div class="box-header with-border d-flex justify-content-between align-items-center">
                    <h4 class="box-title">Rekap Karyawan Direct & Indirect</h4>
                    <div>
                        <select id="filter-direct-indirect" class="form-select form-select-sm">
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row align-items-center">
                        <div class="col-lg-7 col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-direct-indirect">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Keterangan</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel-rekap-direct-indirect">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Memuat data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-5 col-12 text-center">
                            <div id="chart-direct-indirect" style="height:250px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-6 col-12">
            <div class="box">
                <div class="box-header with-border d-flex justify-content-between align-items-center">
                    <h4 class="box-title">Rekap Sinas</h4>
                    <div>
                        <select id="filter-sinas" class="form-select form-select-sm">
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row align-items-center">
                        <div class="col-lg-7 col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-sinas">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Keterangan</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel-rekap-sinas">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Memuat data...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-5 col-12 text-center">
                            <div id="chart-sinas" style="height:250px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-12">
        <div class="box">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title">Rekap Karyawan Dalam & Luar Desa Warung Bambu</h4>
            </div>

            <div class="box-body">
                <div class="row align-items-center">
                    <div class="col-lg-7 col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-warungbambu">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Keterangan</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel-rekap-warungbambu">
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-5 col-12 text-center">
                        <div id="chart-warungbambu" style="height:250px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection