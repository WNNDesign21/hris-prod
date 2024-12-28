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
                <div class="box">
                    <div class="box-body">
                        <form action="#"  id="filterTable">
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="warehouse" class="form-label">Warehouse</label>
                                            <select name="warehouse" id="warehouse" style="width: 100%;" select2>
                                                <option selected value="">Pilih Warehouse</option>
                                            </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label class="form-label">Organisasi</label>
                                        <select class="form-select" style="width: 100%;" id="shift" name="shift">
                                            <option value="" selected>Select Shift</option>
                                            <option value="1" >1</option>
                                            <option value="2" >2</option>
                                            <option value="3" >3</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label class="form-label">Line</label>
                                        <select class="form-select" style="width: 100%;" id="line" name="line">
                                            <option value="" selected>Select Line</option>
                                            <option value="1" >1</option>
                                            <option value="2" >2</option>
                                            <option value="3" >3</option>
                                            <option value="4" >4</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>

                    </div>

                </div>
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
                            <th>Processed</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>

</div>
@endsection