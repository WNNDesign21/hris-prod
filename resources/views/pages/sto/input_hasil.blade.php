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
                    <h3 class="page-title">Input Hasil STO</h3>
                </div>
            </div>
        </div>
        <section class="content">
            <form action="{{ route('sto.store-hasil') }}" method="POST" id="form-hasil-sto">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="form-group">
                                            <label for="no_label" class="form-label">No. Label</label>
                                            <select class="form-select" name="no_label" id="no_label" style="width: 100%;"
                                                select2>
                                                <option value="" disabled selected>Pilih No Label</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="part_code" class="form-label">Search Product</label>
                                            <select class="form-select" name="product_id" id="product_id"
                                                style="width: 100%;">
                                                <option selected value="">Cari Product disini...</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="customer" class="form-label">Customer</label>
                                            <select class="form-select" name="customer" id="customer" style="width: 100%;"
                                                select2>
                                                <option selected value="" disabled>Pilih Customer</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="identitas_lot" class="form-label">Identitas (LOT)</label>
                                            <input type="name" class="form-control" id="identitas_lot"
                                                name="identitas_lot">
                                        </div>
                                        <div class="form-group">
                                            <label for="quantity" class="form-label">Quantity</label>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control"
                                                    placeholder="Insert "id="quantity" name="quantity" value="0">
                                                <span class="input-group-text" id="quantity_uom"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="wh_name" class="form-label">Warehouse</label>
                                            <input type="text" class="form-control" id="wh_name" name="wh_name"
                                                readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="location_area" class="form-label">Lokasi Area</label>
                                            <input type="text" class="form-control" id="location_area" name="location_area"
                                                >
                                        </div>
                                        <div class="form-group">
                                            <label for="part_name" class="form-label">Part Name</label>
                                            <input type="text" class="form-control" id="part_name" name="part_name"
                                                readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="part_desc" class="form-label">Part Number</label>
                                            <input type="text" class="form-control" id="part_desc" name="part_desc"
                                                readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="part_code" class="form-label">Part Code</label>
                                            <input type="text" class="form-control" id="part_code" name="part_code"
                                                readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="model" class="form-label">Model</label>
                                            <input type="text" class="form-control" id="model" name="model"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-center mt-3">
                                    <button type="submit" class="btn btn-block btn-primary" style="width: 100%;"><i
                                            class="glyphicon glyphicon-ok-sign"></i> Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="box">
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="table-hasil-sto" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No Label</th>
                                            <th>Customer</th>
                                            <th>Warehouse</th>
                                            <th>Lokasi Area</th>
                                            <th>Part Code</th>
                                            <th>Part Name</th>
                                            <th>Part Number</th>
                                            <th>Quantity</th>
                                            <th>Identitas (LOT)</th>
                                            <th>Updated</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>

    @include('pages.sto.modal_edit_data')
@endsection
