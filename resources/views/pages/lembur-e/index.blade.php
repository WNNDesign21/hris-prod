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
            <div class="box mb-0">
                <div class="box-body">
                    <div id="chartMonthlyLemburPerDepartemen" class="dask evt-cal min-h-400 p-4"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-6 col-12">
            <div class="box mb-0">
                <div class="box-body">
                    <div id="chartWeeklyLemburPerDepartemen" class="dask evt-cal min-h-300 p-4"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="box mb-0">
                <div class="box-body">
                    <div id="chartCurrentMonthLemburPerDepartemen" class="dask evt-cal min-h-300 p-4"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
