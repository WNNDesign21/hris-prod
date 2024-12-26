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
                <h3 class="page-title">Register No. Label STO</h3>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <form action="{{route('sto.store-label')}}" method="POST" id="form-label-sto">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="start_no_label" class="form-label"> Start No. Label</label>
                                        <input type="number" class="form-control" id="start_no_label" name="start_label" placeholder="No. Label">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="end_no_label" class="form-label"> End No. Label</label>
                                        <input type="number" class="form-control" id="end_no_label" name="end_label" placeholder="No. Label">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label for="wh_id" class="form-label"> Pilih Warehouse</label>
                                        <select class="form-select" name="wh_id" id="wh_id" style="width: 100%;" select2>
                                            <option selected value="">Pilih Lokasi Area</option>
                                        </select>                                    
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
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

@endsection