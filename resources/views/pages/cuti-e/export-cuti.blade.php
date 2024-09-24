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
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <h4 class="box-title">Export Data <br><small>Note : Pilihan menentukan kolom pada hasil export.</small>
                    </h4>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-12">
                            <form action="{{ route('cutie.export.cuti') }}" method="POST" enctype="multipart/form-data"
                                id="form-export-cuti">
                                @csrf
                                <h5 class="text-bold">Departemen</h5>
                                <div class="form-group">
                                    <select class="form-control" name="departemen_id" id="departemen_id">
                                        <option value="all" selected>All Departemen</option>
                                        @foreach ($departemens as $item)
                                            <option value="{{ $item->id_departemen }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <hr>

                                <h5 class="text-bold">Jenis Cuti</h5>
                                <div class="form-group">
                                    <div class="demo-checkbox">
                                        <input type="checkbox" id="pribadi" name="pribadi" value="Y"
                                            class="filled-in chk-col-primary" />
                                        <label for="pribadi" class="mb-0">
                                            <h5>Pribadi/Tahunan</h5>
                                        </label>
                                        <input type="checkbox" id="khusus" name="khusus" value="Y"
                                            class="filled-in chk-col-primary" />
                                        <label for="khusus" class="mb-0">
                                            <h5>Khusus</h5>
                                        </label>
                                        <input type="checkbox" id="sakit" name="sakit" value="Y"
                                            class="filled-in chk-col-primary" />
                                        <label for="sakit" class="mb-0">
                                            <h5>Sakit</h5>
                                        </label>
                                    </div>
                                </div>
                                <hr>

                                <h5 class="text-bold mb-0">Range Data</h5>
                                <small>Note : Kolom ini akan mengambil semua data cuti yang
                                    <strong>rencana mulai
                                        cuti</strong> nya berada pada range yang dipilih</small>
                                <div class="form-group mt-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="from">From</label>
                                            <input type="date" name="from" id="from" class="form-control">
                                        </div>
                                        <div class="col-6">
                                            <label for="to">To</label>
                                            <input type="date" name="to" id="to" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                {{-- <h5 class="text-bold">Status Dokumen</h5>
                                <div class="form-group">
                                    <div class="demo-checkbox">
                                        <input type="checkbox" id="waiting" name="waiting" value="Y"
                                            class="filled-in chk-col-primary" />
                                        <label for="waiting" class="mb-0">
                                            <h5>WAITING</h5>
                                        </label>
                                        <input type="checkbox" id="approved" name="approved" value="Y"
                                            class="filled-in chk-col-primary" />
                                        <label for="approved" class="mb-0">
                                            <h5>APPROVED</h5>
                                        </label>
                                        <input type="checkbox" id="rejected" name="rejected" value="Y"
                                            class="filled-in chk-col-primary" />
                                        <label for="rejected" class="mb-0">
                                            <h5>REJECTED</h5>
                                        </label>
                                    </div>
                                </div>
                                <hr>

                                <h5 class="text-bold">Status Cuti</h5>
                                <div class="form-group">
                                    <div class="demo-checkbox">
                                        <input type="checkbox" id="scheduled" name="scheduled" value="Y"
                                            class="filled-in chk-col-primary" />
                                        <label for="scheduled" class="mb-0">
                                            <h5>SCHEDULED</h5>
                                        </label>
                                        <input type="checkbox" id="onleave" name="onleave" value="Y"
                                            class="filled-in chk-col-primary" />
                                        <label for="onleave" class="mb-0">
                                            <h5>ON LEAVE</h5>
                                        </label>
                                        <input type="checkbox" id="completed" name="completed" value="Y"
                                            class="filled-in chk-col-primary" />
                                        <label for="completed" class="mb-0">
                                            <h5>COMPLETED</h5>
                                        </label>
                                    </div>
                                </div> --}}
                            </form>
                            <div class="row">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="button" class="btn btn-success" id="btnExport">Export</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
