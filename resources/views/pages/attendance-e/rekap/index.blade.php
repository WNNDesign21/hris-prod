@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-attendance')
@endsection

@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-6">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Rekap Presensi</h4>
                    </div>
                </div>
                <div class="box-body">
                    <form action="{{ route('attendance.rekap.export-rekap') }}" method="POST" enctype="multipart/form-data"
                        id="form-export-rekap">
                        @csrf
                        <div class="row">
                            @if (auth()->user()->hasRole('personalia'))
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="departemen">DEPARTEMEN</label>
                                        <select name="departemen[]" id="departemen" class="form-control"
                                            style="width: 100%;" multiple>
                                            @foreach ($departemens as $item)
                                                <option value="{{ $item->id_departemen }}">{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="start">Start Periode</label>
                                    <input type="date" class="form-control" name="start" id="start">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="end">End Periode</label>
                                    <input type="date" class="form-control" name="end" id="end">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 p-4 d-flex justify-content-end gap-2">
                                <button type="button" class="waves-effect waves-light btn btn-danger btnReset"><i
                                        class="fas fa-history"></i> Reset</button>
                                <button type="submit" class="btn waves-effect waves-light btn-success"><i
                                        class="fas fa-file-export"></i>
                                    Rekap</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
