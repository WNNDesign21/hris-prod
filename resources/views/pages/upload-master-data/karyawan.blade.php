@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

{{-- @section('header')
    <header class="main-header">
        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top">
            <div class="app-menu">
                <ul class="header-megamenu nav">
                    <li class="btn-group nav-item d-md-none">
                        <a href="#" class="waves-effect waves-light nav-link push-btn" data-toggle="push-menu"
                            role="button">
                            <span class="icon-Align-left"><span class="path1"></span><span class="path2"></span><span
                                    class="path3"></span></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="navbar-custom-menu r-side">
                <ul class="nav navbar-nav gap-1">
                    <li class="dropdown user user-menu">
                        <a href="#" class="btn btn-light dropdown-toggle position-relative" data-bs-toggle="dropdown"
                            title="User">
                            <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                        </a>
                        <ul class="dropdown-menu animated flipInX">
                            <li class="user-body">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                    <i class="ti-lock text-muted me-2"></i>{{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
@endsection --}}

{{-- @section('navbar')
    @include('layouts.navbar-attendance')
@endsection --}}

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-body">
                    <form action="{{ route('upload-karyawan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="">Upload Karyawan</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="file" name="upload" id="upload" class="form-control" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
