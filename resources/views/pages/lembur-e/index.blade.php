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
                <div class="box-body p-2">
                    <div id="chartMonthlyLemburPerDepartemen" class="dask evt-cal min-h-400"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-6 col-12">
            <div class="box mb-0">
                <div class="box-body p-2">
                    <div id="chartWeeklyLemburPerDepartemen" class="dask evt-cal min-h-300"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="box mb-0">
                <div class="box-body p-2">
                    <div id="chartCurrentMonthLemburPerDepartemen" class="dask evt-cal min-h-300"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
