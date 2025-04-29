@extends('layouts.superuser-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header-superuser')
@endsection

@section('navbar')
    @include('layouts.navbar-superuser')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <h4 class="box-title">System Setting</h4>
                </div>
                <div class="box-body">
                    <div class="row d-flex justify-content-center align-items-center">
                        <div class="col-12 col-lg-6">
                            <label for="">Login Image</label>
                            <div class="form-group">
                                <div class="d-flex justify-content-center">
                                    <a id="linkFoto"
                                        href="{{ file_exists(public_path('storage/system/setting/app_logo.jpg')) ? asset('storage/system/setting/app_logo.jpg') : asset('img/tcf/exist-logo-compress.jpg') }}"
                                        class="image-popup-vertical-fit" data-title="Login Image">
                                        <img id="imageReview"
                                            src="{{ file_exists(public_path('storage/system/setting/app_logo.jpg')) ? asset('storage/system/setting/app_logo.jpg') : asset('img/tcf/exist-logo-compress.jpg') }}"
                                            alt="Image Foto" style="width: 100%;height: 300px;" class="img-fluid">
                                    </a>
                                </div>
                                <div class="btn-group d-flex justify-content-center mt-2">
                                    <button type="button" class="btn btn-primary" id="btnUpload"><i
                                            class="fas fa-upload"></i> Upload</button>
                                    <button type="button" class="btn btn-danger" id="btnReset"><i
                                            class="fas fa-trash"></i> Reset</button>
                                </div>
                                <input type="file" name="app_logo" id="app_logo" class="form-control"
                                    style="display: none;">
                            </div>
                            <div class="form-group mb-5">
                                <label class="form-label">Web Icon</label>
                                <input type="file" class="form-control" id="app_icon" name="app_icon" accept=".ico">
                                <small class="text-muted">Note : Hanya boleh mengupload file berextension .ico</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
