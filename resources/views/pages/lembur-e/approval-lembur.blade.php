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
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Approval Lembur</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info waves-effect btnReload"><i
                                    class="fas fa-sync-alt"></i></button>
                            @if (auth()->user()->hasRole('personalia'))
                                <button type="button" class="btn btn-warning waves-effect btnFilter"><i
                                        class="fas fa-filter"></i></button>
                            @endif
                            @if (auth()->user()->hasRole('atasan') &&
                                    (auth()->user()->karyawan->posisi[0]->jabatan_id == '3' ||
                                        auth()->user()->karyawan->posisi[0]->jabatan_id == '4'))
                                <button type="button" class="btn btn-success waves-effect btnMustChecked"><i
                                        class="far fa-check-circle"></i> Must Checked</button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="approval-table" class="table table-striped table-bordered display nowrap"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID Lembur</th>
                                    <th>Issued At</th>
                                    <th>Rencana Mulai</th>
                                    <th>Issued By</th>
                                    <th>Departemen</th>
                                    <th>Jenis Hari</th>
                                    <th>Total Durasi</th>
                                    <th>Total Nominal</th>
                                    <th>Status</th>
                                    <th>Plan Checked</th>
                                    <th>Plan Approved</th>
                                    <th>Plan Reviewed</th>
                                    <th>Plan Legalized</th>
                                    <th>Actual Checked</th>
                                    <th>Actual Approved</th>
                                    <th>Actual Reviewed</th>
                                    <th>Actual Legalized</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pages.lembur-e.modal-approval-lembur')
    @include('pages.lembur-e.modal-detail-lembur')
    @include('pages.lembur-e.modal-aktual-approval-lembur')
    @include('pages.lembur-e.modal-reject-lembur')
    @include('pages.lembur-e.modal-filter-lembur')
    @include('pages.lembur-e.modal-cross-check-approval-lembur')
@endsection
