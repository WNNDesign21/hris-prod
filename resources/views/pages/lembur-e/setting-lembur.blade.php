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
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title">Setting Lembur</h4>
                    </div>
                </div>
                <div class="box-body">
                    <form action="{{ route('lembure.setting-lembur.update') }}" method="POST" enctype="multipart/form-data"
                        id="form-setting-lembur">
                        @csrf
                        @method('PATCH')
                        {{-- PEMBAGI UPAH LEMBUR --}}
                        <div class="row">
                            <div class="col-lg-3 col-12 p-4">
                                <h4>Pembagi Upah Lembur <span class="text-danger">*</span></h4>
                                <input type="number" min="0" class="form-control" name="pembagi_upah_lembur_harian"
                                    id="pembagi_upah_lembur_harian"
                                    value="{{ $setting_lembur['pembagi_upah_lembur_harian'] }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3 col-12 p-4">
                                <h4>Uang Makan <span class="text-danger">*</span></h4>
                                <input type="number" min="0" class="form-control" name="uang_makan" id="uang_makan"
                                    value="{{ $setting_lembur['uang_makan'] }}">
                            </div>
                        </div>

                        {{-- JAM ISTIRAHAT --}}
                        <div class="row">
                            <div class="col-lg-6 col-12 p-4">
                                <h4>Jam Istirahat <span class="text-danger">*</span></h4>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Start (1)</label>
                                                <input type="time" name="jam_istirahat_mulai_1"
                                                    id="jam_istirahat_mulai_1" class="form-control" style="width: 100%;"
                                                    value="{{ $setting_lembur['jam_istirahat_mulai_1'] }}">

                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label for="">End (1)</label>
                                            <input type="time" name="jam_istirahat_selesai_1"
                                                id="jam_istirahat_selesai_1" class="form-control" style="width: 100%;"
                                                value="{{ $setting_lembur['jam_istirahat_selesai_1'] }}">

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Start (2)</label>
                                                <input type="time" name="jam_istirahat_mulai_2"
                                                    id="jam_istirahat_mulai_2" class="form-control" style="width: 100%;"
                                                    value="{{ $setting_lembur['jam_istirahat_mulai_2'] }}">

                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label for="">End (2)</label>
                                            <input type="time" name="jam_istirahat_selesai_2"
                                                id="jam_istirahat_selesai_2" class="form-control" style="width: 100%;"
                                                value="{{ $setting_lembur['jam_istirahat_selesai_2'] }}">

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Start (3)</label>
                                                <input type="time" name="jam_istirahat_mulai_3"
                                                    id="jam_istirahat_mulai_3" class="form-control" style="width: 100%;"
                                                    value="{{ $setting_lembur['jam_istirahat_mulai_3'] }}">

                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label for="">End (3)</label>
                                            <input type="time" name="jam_istirahat_selesai_3"
                                                id="jam_istirahat_selesai_3" class="form-control" style="width: 100%;"
                                                value="{{ $setting_lembur['jam_istirahat_selesai_3'] }}">

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Start (Jumat)</label>
                                                <input type="time" name="jam_istirahat_mulai_jumat"
                                                    id="jam_istirahat_mulai_jumat" class="form-control" style="width: 100%;"
                                                    value="{{ $setting_lembur['jam_istirahat_mulai_jumat'] }}">

                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label for="">End (Jumat)</label>
                                            <input type="time" name="jam_istirahat_selesai_jumat"
                                                id="jam_istirahat_selesai_jumat" class="form-control"
                                                style="width: 100%;"
                                                value="{{ $setting_lembur['jam_istirahat_selesai_jumat'] }}">

                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <hr>
                        </div>

                        {{-- INSENTIF --}}
                        <div class="row">
                            <div class="col-lg-6 col-12 p-4">
                                <h4>Insentif Section Head <span class="text-danger">*</span></h4>
                                <hr>
                                <div class="form-group">
                                    <label for="">Jam Ke-1</label>
                                    <input type="number" min="0" name="insentif_section_head_1"
                                        id="insentif_section_head_1" class="form-control" style="width: 100%;"
                                        value="{{ $setting_lembur['insentif_section_head_1'] }}">

                                </div>
                                <div class="form-group">
                                    <label for="">Jam Ke-2</label>
                                    <input type="number" min="0" name="insentif_section_head_2"
                                        id="insentif_section_head_2" class="form-control" style="width: 100%;"
                                        value="{{ $setting_lembur['insentif_section_head_2'] }}">

                                </div>
                                <div class="form-group">
                                    <label for="">Jam Ke-3</label>
                                    <input type="number" min="0" name="insentif_section_head_3"
                                        id="insentif_section_head_3" class="form-control" style="width: 100%;"
                                        value="{{ $setting_lembur['insentif_section_head_3'] }}">

                                </div>
                                <div class="form-group">
                                    <label for="">Jam Ke-4 (Weekend)</label>
                                    <input type="number" min="0" name="insentif_section_head_4"
                                        id="insentif_section_head_4" class="form-control" style="width: 100%;"
                                        value="{{ $setting_lembur['insentif_section_head_4'] }}">

                                </div>
                            </div>
                            <hr>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-12 p-4">
                                <h4>Insentif Departemen Head <span class="text-danger">*</span></h4>
                                <div class="form-group">
                                    <label for="">Jam Ke-4 (Weekend)</label>
                                    <input type="number" min="0" name="insentif_department_head_4"
                                        id="insentif_department_head_4" class="form-control" style="width: 100%;"
                                        value="{{ $setting_lembur['insentif_department_head_4'] }}">

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 p-4 d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
