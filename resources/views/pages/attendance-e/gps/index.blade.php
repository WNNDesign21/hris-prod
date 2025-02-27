@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-attendance')
@endsection

@section('content')
    <div class="row d-flex justify-content-center align-items-center" style="height: 60vh;">
        <div class="col-6">
            <div class="row d-flex justify-content-center">
                <div class="col-12">
                    <form action="{{ route('attendance.gps.store') }}" enctype="multipart/form-data" id="form-input"
                        method="POST">
                        @csrf
                        <input type="hidden" name="longitude" id="longitude" class="form-control" readonly>
                        <input type="hidden" name="latitude" id="latitude" class="form-control" readonly>
                        <input type="hidden" name="status" id="status" class="form-control" readonly>
                        <input type="hidden" name="image" id="image">
                    </form>
                </div>
                <div class="text-center">
                    <h1 id="realtimeClock" class="mb-3"></h1>
                </div>
                <div class="form-group">
                    <label for="">Type</label>
                    <select name="type" id="type" class="form-control" style="width: 100%;">
                        <option value="VS">VENDOR STAY</option>
                        <option value="TL">TUGAS LUAR</option>
                    </select>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-6">
                    <a href="javascript:void(0)" class="box pull-up btnIn">
                        <div class="box-body text-center bg-success">
                            <h3>IN</h3>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="javascript:void(0)" class="box pull-up btnOut">
                        <div class="box-body text-center bg-danger">
                            <h3>OUT</h3>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @include('pages.attendance-e.gps.modal-input')
@endsection
