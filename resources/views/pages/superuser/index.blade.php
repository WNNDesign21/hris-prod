@extends('layouts.superuser-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    <header class="main-header">
        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top">
            <div class="app-menu">
                <ul class="header-megamenu nav">
                    <li class="btn-group nav-item d-none">
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
@endsection

@section('content')
    <div class="row d-flex justify-content-center p-5">
        <div class="col-lg-12 col-12">
            <div class="row d-flex justify-content-start p-5">
                {{-- CARD MANAJEMEN MASTER DATA --}}
                <div class="col-lg-6 col-12">
                    <a href="#" class="box pull-up">
                        <div class="box-body">
                            <div class="d-flex align-items-center">
                                <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                                    <span class="fs-30 icon-Bulb1"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span><span
                                            class="path4"></span></span>
                                </div>
                                <div class="ms-15">
                                    <h5 class="mb-0">Master Data Management</h5>
                                    <p class="text-fade fs-12 mb-0">Manajemen Data Master untuk Superadmin</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- CARD MANAJEMEN MASTER DATA --}}
                <div class="col-lg-6 col-12">
                    <a href="#" class="box pull-up">
                        <div class="box-body">
                            <div class="d-flex align-items-center">
                                <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                                    <span class="fs-30 icon-Settings1"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span><span
                                            class="path4"></span></span>
                                </div>
                                <div class="ms-15">
                                    <h5 class="mb-0">System Setting</h5>
                                    <p class="text-fade fs-12 mb-0">Setting general untuk sistem</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- CARD User Management --}}
                <div class="col-lg-6 col-12">
                    <a href="#" class="box pull-up">
                        <div class="box-body">
                            <div class="d-flex align-items-center">
                                <div class="icon bg-primary-light rounded-circle w-60 h-60 text-center l-h-80">
                                    <span class="fs-30 icon-User-folder"><span class="path1"></span><span
                                            class="path2"></span><span class="path3"></span><span
                                            class="path4"></span></span>
                                </div>
                                <div class="ms-15">
                                    <h5 class="mb-0">User Management</h5>
                                    <p class="text-fade fs-12 mb-0">Manajemen User untuk Superadmin</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
