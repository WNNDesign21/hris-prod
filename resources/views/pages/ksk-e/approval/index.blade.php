@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-ksk')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Approval KSK</h4>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info waves-effect btnReload"><i
                                    class="fas fa-sync-alt"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-pills mb-20">
                        <li class="nav-item"> <a href="#must-approved" class="nav-link active" data-bs-toggle="tab"
                                aria-expanded="false">Must Approved</a> </li>
                        <li class="nav-item"> <a href="#history" class="nav-link" data-bs-toggle="tab"
                                aria-expanded="false">History</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="must-approved" class="tab-pane active">
                            <div class="table-responsive">
                                <table id="must-approved-table" class="table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID KSK</th>
                                            <th>Divisi</th>
                                            <th>Departemen</th>
                                            <th>Atasan Langsung</th>
                                            <th>Release Date</th>
                                            <th>Leader</th>
                                            <th>Section</th>
                                            <th>Dept.Head</th>
                                            <th>Div.Head</th>
                                            <th>Plant Head</th>
                                            <th>Director</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div id="history" class="tab-pane">
                            <div class="table-responsive">
                                <table id="history-table" class="table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID KSK</th>
                                            <th>Divisi</th>
                                            <th>Departemen</th>
                                            <th>Atasan Langsung</th>
                                            <th>Release Date</th>
                                            <th>Leader</th>
                                            <th>Section</th>
                                            <th>Dept.Head</th>
                                            <th>Div.Head</th>
                                            <th>Plant Head</th>
                                            <th>Director</th>
                                            <th>Status</th>
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
    @include('pages.ksk-e.approval.modal-approval')
@endsection
