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
        <div class="col-lg-6 col-12">
            <div class="box no-shadow mb-0">
                <div class="box-body p-2">
                    <div id="calendar" class="dask evt-cal min-h-400"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
