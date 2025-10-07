@extends('layouts.guest-layout')

@section('content')
    <div class="content-header">
        <div class="d-flex justify-content-between mb-3">
            <div class="col-6 d-flex justify-content-start align-items-center gap-3">
                <div class="spinner-grow text-danger" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div>
                    <h1 class="page-title ml-3">Live Attendance</h1>
                </div>
            </div>
            <div class="col-6 d-flex justify-content-end align-items-center">
                <div class="text-end">
                    <h3>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}</h3>
                    <p>PT ADYAWINSA STAMPING INDUSTRIES</p>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-xl-6 col-12 align-self-center">
                <div id="chart"></div>
            </div>
            <div class="col-xl-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="data-table" class="table table-hover no-wrap table-bordered" data-page-size="20"
                                style="width: 100%  ">
                                <thead>
                                    <tr>
                                        <th>Karyawan</th>
                                        <th>Departemen</th>
                                        <th>Scan Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
