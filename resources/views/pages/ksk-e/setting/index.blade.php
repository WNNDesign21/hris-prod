@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-ksk')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Setting KSK</h4>
                    </div>
                </div>
                <div class="box-body">
                    <form action="{{ route('ksk.setting.update') }}" method="POST" enctype="multipart/form-data"
                        id="form-input">
                        @csrf
                        <div class="form-group">
                            <label for="dept_it">Departemen IT</label>
                            <select name="dept_it" id="dept_it" class="form-control" style="width: 100%">
                                <option value="{{ $deptIT?->karyawan_id }}" selected>{{ $deptIT?->nama_karyawan }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dept_fat">Departemen Finance</label>
                            <select name="dept_fat" id="dept_fat" class="form-control" style="width: 100%">
                                <option value="{{ $deptFAT?->karyawan_id }}" selected>{{ $deptFAT?->nama_karyawan }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dept_ga">Departemen GA</label>
                            <select name="dept_ga" id="dept_ga" class="form-control" style="width: 100%">
                                <option value="{{ $deptGA?->karyawan_id }}" selected>{{ $deptGA?->nama_karyawan }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="dept_hr">Departemen HR</label>
                            <select name="dept_hr" id="dept_hr" class="form-control" style="width: 100%">
                                <option value="{{ $deptHR?->karyawan_id }}" selected>{{ $deptHR?->nama_karyawan }}</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
