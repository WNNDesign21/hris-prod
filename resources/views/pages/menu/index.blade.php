@extends('layouts.menu-layout')

@section('content')
    {{-- CARD MANAJEMEN MASTER DATA --}}
    {{-- @if (auth()->user()->hasRole('personalia') || auth()->user()->hasRole('super user')) --}}
    <div class="col-lg-4 col-12">
        <a href="{{ route('master-data.dashboard') }}" class="box pull-up">
            <div class="box-body">
                <div class="d-flex align-items-center">
                    <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                        <span class="fs-30 icon-Bulb1"><span class="path1"></span><span class="path2"></span><span
                                class="path3"></span><span class="path4"></span></span>
                    </div>
                    <div class="ms-15">
                        <h5 class="mb-0">Master Data Management</h5>
                        <p class="text-fade fs-12 mb-0">Sistem Manajemen Data Master</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
    {{-- @endif --}}

    {{-- CARD CUTI SYSTEM --}}
    <div class="col-lg-4 col-12">
        <a href="{{ route('cutie.dashboard') }}" class="box pull-up">
            <div class="box-body">
                <div class="d-flex align-items-center">
                    <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                        <span class="fs-30 icon-Bulb1"><span class="path1"></span><span class="path2"></span><span
                                class="path3"></span><span class="path4"></span></span>
                    </div>
                    <div class="ms-15">
                        <h5 class="mb-0">Cuti-e</h5>
                        <p class="text-fade fs-12 mb-0">Sistem Penjadwalan dan Pengajuan Cuti Karyawan</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- CARD CUTI SYSTEM --}}
    <div class="col-lg-4 col-12">
        <a href="#" class="box pull-up">
            <div class="box-body">
                <div class="d-flex align-items-center">
                    <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                        <span class="fs-30 icon-Bulb1"><span class="path1"></span><span class="path2"></span><span
                                class="path3"></span><span class="path4"></span></span>
                    </div>
                    <div class="ms-15">
                        <h5 class="mb-0">Lembure</h5>
                        <p class="text-fade fs-12 mb-0">Sistem Pengajuan dan Penjadwalan Lembur Karyawan</p>
                    </div>
                </div>
            </div>
        </a>
    </div>


    {{-- CARD PRESENSI DAN PAYROLL SYSTEM --}}
    <div class="col-lg-4 col-12">
        <a href="#" class="box pull-up">
            <div class="box-body">
                <div class="d-flex align-items-center">
                    <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                        <span class="fs-30 icon-Chat-check"><span class="path1"></span><span class="path2"></span></span>
                    </div>
                    <div class="ms-15">
                        <h5 class="mb-0">Presensi & Payroll</h5>
                        <p class="text-fade fs-12 mb-0">Sistem Presensi Karyawan & Perhitungan Payroll</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- MONITORING TRANSAKSI SYSTEM --}}
    <div class="col-lg-4 col-12">
        <a href="#" class="box pull-up">
            <div class="box-body">
                <div class="d-flex align-items-center">
                    <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                        <span class="fs-30 icon-Bulb1"><span class="path1"></span><span class="path2"></span><span
                                class="path3"></span><span class="path4"></span></span>
                    </div>
                    <div class="ms-15">
                        <h5 class="mb-0">Monitoring Transaction</h5>
                        <p class="text-fade fs-12 mb-0">Sistem Monitoring Data Transaksi dari Idempiere</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
@endsection
