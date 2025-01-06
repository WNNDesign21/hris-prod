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
            <form action="{{ route('lembure.bypass-lembur.store') }}" method="POST" enctype="multipart/form-data"
                id="form-bypass-lembur">
                @csrf
                <div class="box">
                    <div class="box-header d-flex justify-content-between">
                        <div class="row">
                            <h4 class="box-title">Bypass Lembur</h4>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="issued_by">Issued By</label>
                                    <select name="issued_by" id="issued_by" class="form-control" style="width: 100%;"
                                        required>
                                        <option value="">PILIH ISSUED BY</option>
                                        @foreach ($karyawans as $index => $item)
                                            <option value="{{ $index }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="jenis_hari">Jenis Hari</label>
                                    <select name="jenis_hari" id="jenis_hari" class="form-control" style="width:100%;"
                                        required>
                                        <option value="">PILIH JENIS HARI LEMBUR</option>
                                        <option value="WE">WEEKEND</option>
                                        <option value="WD">WEEKDAY</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-success waves-effect btnAddDetailLembur"><i
                                            class="fas fa-plus"></i>&nbsp;&nbsp;Tambah Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body px-1 mx-4 py-0">
                        <div class="row" id="list-detail-lembur">
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="col-lg-12 p-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
