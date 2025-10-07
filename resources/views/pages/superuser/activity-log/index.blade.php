@extends('layouts.superuser-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header-superuser')
@endsection

@section('navbar')
    @include('layouts.navbar-superuser')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <h4 class="box-title">Activity Log</h4>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info waves-effect btnReload"><i
                                class="fas fa-sync-alt"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="form-label">Created At</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="created_at" name="created_at">
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="form-label">Causer</label>
                                <select name="causer" id="causer" class="form-control">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="activity-log-table" class="table table-striped display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Log Name</th>
                                    <th>Description</th>
                                    <th>Causer</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
