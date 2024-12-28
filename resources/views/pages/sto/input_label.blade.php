@extends('layouts.auth-layout')
@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-sto')
@endsection

@section('content')
    <div class="container-full">
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="me-auto">
                    <h3 class="page-title">Form Register Label</h3>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-body">
                            <form action="{{ route('sto.store-label') }}" method="POST" id="form-label-sto">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="wh_id" class="form-label"> Pilih Warehouse</label>
                                            <select class="form-select" name="wh_id" id="wh_id" style="width: 100%;"
                                                select2>
                                                <option selected value="">Pilih Lokasi Area</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="start_no_label" class="form-label"> Start No. Label</label>
                                            <input type="number" class="form-control" id="start_no_label"
                                                name="start_label" placeholder="No. Label">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="end_no_label" class="form-label"> End No. Label</label>
                                            <input type="number" class="form-control" id="end_no_label" name="end_label"
                                                placeholder="No. Label">
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-center mt-3">
                                        <button type="submit" class="btn btn-block btn-primary" style="width: 100%;"><i
                                                class="glyphicon glyphicon-ok-sign"></i> Registrasi</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="box">
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="table-register-label" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No Label</th>
                                            <th>Issued By</th>
                                            <th>Warehouse</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
