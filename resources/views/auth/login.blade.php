@extends('layouts.guest-layout')

@section('content')
    <div class="content-top-agile p-40">
        <img src="{{ asset('img/tcf/logo.png') }}" alt="TCF Logo" class="rounded-circle">
    </div>
    <div class="p-40 pt-0">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-transparent"><i class="ti-user"></i></span>
                    <input type="text" class="form-control ps-15 bg-transparent @error('email') is-invalid @enderror"
                        placeholder="Username" name="email" id="email">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <div class="input-group mb-3">
                    <span class="input-group-text  bg-transparent"><i class="ti-lock"></i></span>
                    <input type="password" class="form-control ps-15 bg-transparent @error('password') is-invalid @enderror"
                        placeholder="Password" name="password" id="password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check" style="padding-left: 0px;">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary mt-10 text-white" style="width:100%;">SIGN IN</button>
                </div>
            </div>
        </form>
    </div>
@endsection
