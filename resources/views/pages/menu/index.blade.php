@extends('layouts.menu-layout')

@section('content')
    <div class="row d-flex justify-content-center p-5">
        <div class="col-lg-4 col-12">
            <div class="box no-shadow mb-0" id="menu-calendar">
                <div class="box-body p-2">
                    <div id="calendarEvent" class="dask evt-cal min-h-400"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-12">
            <div class="row d-flex justify-content-start p-5">
                {{-- CARD MANAJEMEN MASTER DATA --}}
                @if (auth()->user()->hasRole('personalia') || auth()->user()->hasRole('super user'))
                    <div class="col-lg-6 col-12">
                        <a href="{{ route('master-data.dashboard') }}" class="box pull-up">
                            <div class="box-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                                        <span class="fs-30 icon-Bulb1"><span class="path1"></span><span
                                                class="path2"></span><span class="path3"></span><span
                                                class="path4"></span></span>
                                    </div>
                                    <div class="ms-15">
                                        <h5 class="mb-0">Master Data Management</h5>
                                        <p class="text-fade fs-12 mb-0">Sistem Manajemen Data Master</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                {{-- CARD CUTI SYSTEM --}}
                <div class="col-lg-6 col-12">
                    <a href="{{ !auth()->user()->hasRole('member') ? route('cutie.dashboard') : route('cutie.pengajuan-cuti') }}"
                        class="box pull-up">
                        <div class="box-body position-relative">
                            {{-- <span class="position-absolute top-0 start-95 translate-middle badge bg-danger">
                                <i class="ti-bell"></i>
                            </span> --}}
                            <div class="d-flex align-items-center">
                                <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                                    <span class="fs-30 icon-Bed"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span><span
                                            class="path4"></span></span>
                                </div>
                                <div class="ms-15">
                                    <h5 class="mb-0">Cuti-E</h5>
                                    <p class="text-fade fs-12 mb-0">Sistem Penjadwalan dan Pengajuan Cuti Karyawan</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- CARD LEMBUR SYSTEM --}}
                @if (auth()->user()->hasRole('personalia') || auth()->user()->hasRole('atasan') || !$lembure['has_leader'])
                    {{-- CARD LEMBUR SYSTEM --}}
                    <div class="col-lg-6 col-12">
                        <a href="{{ auth()->user()->hasRole('personalia') || auth()->user()->karyawan->posisi[0]->jabatan_id <= 3 ? route('lembure.dashboard') : route('lembure.pengajuan-lembur') }}"
                            class="box pull-up">
                            <div class="box-body position-relative">
                                @if ($lembure['approval_lembur'] + $lembure['pengajuan_lembur'] > 0)
                                    <span class="position-absolute top-0 start-95 translate-middle badge bg-danger">
                                        <i class="ti-bell"></i>
                                        {{ $lembure['approval_lembur'] + $lembure['pengajuan_lembur'] }}
                                    </span>
                                @endif
                                <div class="d-flex align-items-center">
                                    <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                                        <span class="fs-30 icon-Timer"><span class="path1"></span><span
                                                class="path2"></span><span class="path3"></span><span
                                                class="path4"></span></span>
                                    </div>
                                    <div class="ms-15">
                                        <h5 class="mb-0">Lembur-E</h5>
                                        <p class="text-fade fs-12 mb-0">Sistem Pengajuan dan Penjadwalan Lembur Karyawan</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- CARD PRESENSI DAN PAYROLL SYSTEM --}}
        {{-- <div class="col-lg-4 col-12">
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
    </div> --}}

        {{-- MONITORING TRANSAKSI SYSTEM --}}
        {{-- <div class="col-lg-4 col-12">
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
    </div> --}}
    </div>
@endsection
