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
                <table id="table-hasil-sto" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No Label</th>
                            <th>Customer</th>
                            <th>Part Code</th>
                            <th>Part Name</th>
                            <th>Part Number</th>
                            <th>Model</th>
                            <th>Warehouse</th>
                            <th>Quantity</th>
                            <th>Identitas/Lot</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>

</div>
@endsection