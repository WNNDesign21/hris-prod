@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-cutie')
@endsection

@section('content')
    {{-- JUMLAH DASHBOARD --}}
    <div class="row">
        <div class="col-12">
            <div class="box no-shadow mb-0">
                <div class="box-body p-2">
                    <div id="calendar" class="dask evt-cal min-h-400"></div>
                </div>
            </div>
        </div>
        {{-- <div class="col-lg-6 col-12">
            <div class="row">
                <div class="col-12">
                    <div class="box no-shadow">
                        <div class="box-header with-border">
                            <h4 class="box-title">{{ $detail_cuti_title }}</h4>
                        </div>
                        <div class="box-body">
                            <div style="min-height: 198px;">
                                <div id="detail-cuti-chart">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="box no-shadow">
                        <div class="box-header with-border">
                            <h4 class="box-title">{{ $jenis_cuti_title }}</h4>
                        </div>
                        <div class="box-body">
                            <div style="min-height: 198px;">
                                <div id="jenis-cuti-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
    @include('pages.cuti-e.modal-event-cuti')
@endsection
