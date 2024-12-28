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
                <h3 class="page-title">Table Hasil STO</h3>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="row">
            <div class="col-12">
                {{-- <div class="box"> --}}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label for="wh_id" class="form-label" >Filter Warehouse</label>
                                    {{-- <div class="input-group"> --}}
                                        <select class="form-control" name="wh_id[]" id="wh_id" style="width: 100%;" select2 multiple>
                                            <option value="">Pilih Warehouse</option>
                                        </select>
                                        <button id="submit-filter" type="button" class="btn btn-info">Filter</button>
                                        <button id="export-excel-button" type="button" class="btn btn-success">Download</button>
                                    {{-- </div> --}}
                                    
                                        
                                </div>
                            </div>
                        </div>
                        
                    {{-- </div> --}}
                </div>
                <div class="box">
                    {{-- <br>
                <div class="col-md-2 col-12">
                    <button id="export-excel-button" class="btn btn-info" type="button">Export to Excel</button>
                </div> --}}
                    <div class="box-body">
                        <table id="table-hasil-sto" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Warehouse</th>
                                    <th>Locator</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Product Number</th>
                                    <th>Classification</th>
                                    <th>QTY Book</th>
                                    <th>QTY Count</th>
                                    <th>Balance</th>
                                    <th>Organization</th>
                                    <th>Processed</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection