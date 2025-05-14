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
                    <h4 class="box-title">Export Data <br><small>Note : Pilihan menentukan kolom pada hasil
                            export.</small>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-12">
                            <form action="{{ route('cutie.export.export') }}" method="POST" enctype="multipart/form-data"
                                id="form-export-cuti">
                                @csrf
                                <h5 class="text-bold">Departemen</h5>
                                <div class="form-group">
                                    <select class="form-control" name="departemen_id" id="departemen_id">
                                        <option value="all" selected>All Departemen</option>
                                        @foreach ($departemen as $item)
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
                                    </div>
                                </div>
                                <hr>

                                <h5 class="text-bold mb-0">Tahun</h5>
                                <div class="form-group mt-3">
                                    <select name="tahun" id="tahun" class="form-control">
                                        @for ($i = 2023; $i <= date('Y'); $i++)
                                            <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                                {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <h5 class="text-bold mb-0">Bulan <br><small>Note : Abaikan pilihan ini jika ingin merekap
                                        data
                                        tahunan.</small></h5>
                                <div class="form-group mt-3">
                                    <select name="bulan" id="bulan" class="form-control">
                                        <option value="">Pilih Bulan</option>
                                        <option value="01">Januari</option>
                                        <option value="02">Februari</option>
                                        <option value="03">Maret</option>
                                        <option value="04">April</option>
                                        <option value="05">Mei</option>
                                        <option value="06">Juni</option>
                                        <option value="07">Juli</option>
                                        <option value="08">Agustus</option>
                                        <option value="09">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
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
