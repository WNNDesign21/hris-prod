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
                        <h4 class="box-title">Export Report Lembur</h4>
                    </div>
                </div>
                <div class="box-body">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-export-report-lembur">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12 p-4 d-flex justify-content-end">
                                <button type="submit" class="btn btn-success"><i class="fas fa-file-export"></i>
                                    Export</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
