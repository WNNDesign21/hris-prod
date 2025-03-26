@extends('layouts.menu-layout')

@section('content')
    <div class="row d-flex justify-content-center p-5">
        <div class="col-lg-4 col-12">
            @if (!auth()->user()->hasRole('security'))
                <div class="box no-shadow mb-0" id="menu-calendar">
                    <div class="box-body p-2">
                        <div id="calendarEvent" class="dask evt-cal min-h-400"></div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-lg-8 col-12">
            <div class="row d-flex justify-content-start p-5">
                {{-- CARD MANAJEMEN MASTER DATA --}}
                @if (auth()->user()->hasRole('personalia') || auth()->user()->hasRole('admin-dept'))
                    @if (auth()->user()->hasRole('personalia'))
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
                @endif

                {{-- CARD ATTENDANCE SYSTEM --}}
                <div class="col-lg-6 col-12">
                    <a href="{{ route('attendance.gps') }}" class="box pull-up">
                        <div class="box-body">
                            <div class="d-flex align-items-center">
                                <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                                    <span class="fs-30 icon-Done-circle"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span><span
                                            class="path4"></span></span>
                                </div>
                                <div class="ms-15">
                                    <h5 class="mb-0">Attendance-E</h5>
                                    <p class="text-fade fs-12 mb-0">Sistem Monitoring & Pengambilan data Presensi dari
                                        Mesin</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                @if (!auth()->user()->hasRole('security'))
                    {{-- CARD CUTI SYSTEM --}}
                    <div class="col-lg-6 col-12">
                        <a href="{{ !auth()->user()->hasRole('member') ? route('cutie.dashboard') : route('cutie.pengajuan-cuti') }}"
                            class="box pull-up">
                            <div class="box-body position-relative">
                                @if ($notification['count_cutie_approval'] + $notification['count_my_cutie'] + $notification['count_rejected_cuti'] > 0)
                                    <span class="position-absolute top-0 start-95 translate-middle badge bg-danger">
                                        <i class="ti-bell"></i>
                                        {{ $notification['count_cutie_approval'] + $notification['count_my_cutie'] + $notification['count_rejected_cuti'] }}
                                    </span>
                                @endif
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
                            <a href="{{ auth()->user()->hasRole('personalia') || (auth()->user()->karyawan && auth()->user()->karyawan->posisi[0]->jabatan_id <= 3) ? route('lembure.dashboard') : route('lembure.pengajuan-lembur') }}"
                                class="box pull-up">
                                <div class="box-body position-relative">
                                    @if ($lembure['approval_lembur'] + $lembure['pengajuan_lembur'] + $lembure['review_lembur'] > 0)
                                        <span class="position-absolute top-0 start-95 translate-middle badge bg-danger">
                                            <i class="ti-bell"></i>
                                            {{ $lembure['approval_lembur'] + $lembure['pengajuan_lembur'] + $lembure['review_lembur'] }}
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
                                            <p class="text-fade fs-12 mb-0">Sistem Pengajuan dan Penjadwalan Lembur Karyawan
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endif


                {{-- CARD IZIN SYSTEM --}}
                <div class="col-lg-6 col-12">
                    <a href="{{ route('izine.pengajuan-izin') }}" class="box pull-up">
                        <div class="box-body position-relative">
                            @if ($izine['total_izine_notification'] > 0)
                                <span class="position-absolute top-0 start-95 translate-middle badge bg-danger">
                                    <i class="ti-bell"></i>
                                    {{ $izine['total_izine_notification'] }}
                                </span>
                            @endif
                            <div class="d-flex align-items-center">
                                <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                                    <span class="fs-30 icon-Clipboard"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span><span
                                            class="path4"></span></span>
                                </div>
                                <div class="ms-15">
                                    <h5 class="mb-0">Izin-E</h5>
                                    <p class="text-fade fs-12 mb-0">Sistem Pengajuan dan Approval Izin Digital</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                {{-- CARD TUGASLUAR SYSTEM --}}
                <div class="col-lg-6 col-12">
                    <a href="{{ auth()->user()->hasRole('personalia') || auth()->user()->hasRole('security') ? route('tugasluare.approval') : route('tugasluare.pengajuan') }}"
                        class="box pull-up">
                        <div class="box-body position-relative">
                            @if ($tugasluare['approval'] + $tugasluare['pengajuan'] > 0)
                                <span class="position-absolute top-0 start-95 translate-middle badge bg-danger">
                                    <i class="ti-bell"></i>
                                    {{ $tugasluare['approval'] + $tugasluare['pengajuan'] }}
                                </span>
                            @endif
                            <div class="d-flex align-items-center">
                                <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                                    <span class="fs-30 icon-Marker"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span><span
                                            class="path4"></span></span>
                                </div>
                                <div class="ms-15">
                                    <h5 class="mb-0">TugasLuar-E</h5>
                                    <p class="text-fade fs-12 mb-0">Sistem Pengajuan & Approval Tugas Luar Digital
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>


                {{-- CARD KSK SYSTEM --}}
                <div class="col-lg-6 col-12">
                    <a href="{{ auth()->user()->hasRole('personalia') ? route('ksk.release') : route('ksk.approval') }}"
                        class="box pull-up">
                        <div class="box-body position-relative">
                            @if ($ksk['total_release_ksk'] + $ksk['total_approval_ksk'] > 0)
                                <span class="position-absolute top-0 start-95 translate-middle badge bg-danger">
                                    <i class="ti-bell"></i>
                                    {{ $ksk['total_release_ksk'] + $ksk['total_approval_ksk'] }}
                                </span>
                            @endif
                            <div class="d-flex align-items-center">
                                <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                                    <span class="fs-30 icon-File"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span><span
                                            class="path4"></span></span>
                                </div>
                                <div class="ms-15">
                                    <h5 class="mb-0">KSK-E</h5>
                                    <p class="text-fade fs-12 mb-0">Sistem Pengisian KSK Digital
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
