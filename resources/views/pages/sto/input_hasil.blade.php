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
                           
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="no_label" class="form-label">No. Label</label>
                                        <select class="form-select" name="no_label" id="no_label" style="width: 100%;" select2>
                                            <option value="" disabled selected>Pilih No Label</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8 col-12">
                                    <div class="form-group">
                                        <label for="part_code" class="form-label">Search Product</label>
                                        <select class="form-select" name="product_id" id="product_id">
                                            <option selected value="">Cari Product disini...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label for="part_name" class="form-label">Part Name</label>
                                        <input type="text" class="form-control" id="part_name" name="part_name" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label for="part_desc" class="form-label">Part Number</label>
                                        <input type="text" class="form-control" id="part_desc" name="part_desc" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label for="part_code" class="form-label">Part Code</label>
                                        <input type="text" class="form-control" id="part_code" name="part_code" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label for="model" class="form-label">Model</label>
                                        <input type="text" class="form-control" id="model" name="model" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label for="customer" class="form-label">Customer</label>
                                        <select class="form-select" name="customer" id="customer" style="width: 100%;" select2>
                                            <option selected value="" disabled>Pilih Customer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label for="wh_name" class="form-label">Lokasi Area</label>
                                        <input type="text" class="form-control" id="wh_name" name="wh_name" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" >
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label for="identitas_lot" class="form-label">Identitas (LOT)</label>
                                        <input type="number" class="form-control" id="identitas_lot" name="identitas_lot" >
                                    </div>
                                </div>
                            </div>
                        </div>   
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                </div>
                    <div class="col-4" style="text-align:center;">
                        <input type="submit" class="btn btn-primary" value="Submit">
                    </div>
            </div>
        </form>
    </section>

</div>


@endsection