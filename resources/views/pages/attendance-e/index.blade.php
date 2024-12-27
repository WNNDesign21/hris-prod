@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-attendancee')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box mb-0">
                <div class="box-body">
                    <div id="chartMonthlyAttendanceDepartment" class="dask evt-cal min-h-400 p-4"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
