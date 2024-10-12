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
                        <h4 class="box-title">List Data Cuti Personal</h4>
                        <br>
                        <br>
                        <small style="width: 85%">Note : Setelah bekerja 12 Bulan berturut-turun, perusahaan mengatur
                            pengambilan hari
                            cuti sebanyak <span class="text-bold text-primary">6 Hari</span> dan
                            karyawan
                            mengatur pengambilan hari cuti sebanyak <span class="text-bold text-primary">6 Hari</span>
                            <br>
                            Jatah Cuti Tahunan ({{ date('Y') }}) adalah <span class="text-bold text-primary"
                                id="sisa_cuti_total_display">{{ auth()->user()->karyawan->sisa_cuti_pribadi + auth()->user()->karyawan->sisa_cuti_bersama }}
                                Hari</span <ul>
                            <li>Sisa Cuti Pribadi ({{ date('Y') }}) adalah <span class="text-bold text-primary"
                                    id="sisa_cuti_pribadi">{{ auth()->user()->karyawan->sisa_cuti_pribadi }}
                                    Hari</span></li>
                            <li>Sisa Cuti Bersama ({{ date('Y') }}) adalah <span class="text-bold text-primary"
                                    id="sisa_cuti_bersama">{{ auth()->user()->karyawan->sisa_cuti_bersama }}
                                    Hari</span></li>
                            <li>Sisa Cuti {{ date('Y') - 1 }} (Hanya berlaku +3 Bulan setelah Reset Cuti) adalah <span
                                    class="text-bold text-primary"
                                    id="sisa_cuti_bersama">{{ auth()->user()->karyawan->sisa_cuti_tahun_lalu }}
                                    Hari</span></li>
                            <li>Hutang Cuti ({{ date('Y') }}) : <span class="text-bold text-primary"
                                    id="hutang_cuti_display">{{ auth()->user()->karyawan->hutang_cuti }} Hari</span>
                            </li>
                            </ul>
                        </small>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info waves-effect btnReload"><i
                                    class="fas fa-sync-alt"></i></button>
                            <button type="button" class="btn btn-success waves-effect btnAdd"><i
                                    class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="personal-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Rencana Mulai</th>
                                    <th>Rencana Selesai</th>
                                    <th>Aktual Mulai</th>
                                    <th>Aktual Selesai</th>
                                    <th>Durasi</th>
                                    <th>Jenis</th>
                                    <th>Checked 1</th>
                                    <th>Checked 2</th>
                                    <th>Approved</th>
                                    <th>Legalized</th>
                                    <th>Status Dokumen</th>
                                    <th>Status</th>
                                    <th>Alasan</th>
                                    <th>Karyawan Pengganti</th>
                                    <th>Created At</th>
                                    <th>Attachment</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pages.cuti-e.modal-pengajuan-cuti')
@endsection
