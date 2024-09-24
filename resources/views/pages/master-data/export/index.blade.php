@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-masterdata')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <h4 class="box-title">Export Data <br><small>Note : Setiap Bagian akan terpisah kedalam Sheet nya
                            masing-masing.</small></h4>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <form action="{{ route('master-data.export.master-data') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <h5 class="text-bold">Karyawan</h5>
                                <div class="form-group">
                                    <div class="demo-checkbox">
                                        <input type="checkbox" id="karyawan_aktif" name="karyawan_aktif" value="Y"
                                            class="filled-in chk-col-primary" />
                                        <label for="karyawan_aktif" class="mb-0">
                                            <small>Aktif</small>
                                        </label>
                                        <input type="checkbox" id="karyawan_nonaktif" name="karyawan_nonaktif"
                                            value="Y" class="filled-in chk-col-primary" />
                                        <label for="karyawan_nonaktif" class="mb-0">
                                            <small>Non Aktif (Terminasi, Resign, Pensiun)</small>
                                        </label>
                                    </div>
                                </div>
                                <hr>

                                <input type="checkbox" id="posisi" name="posisi" value="Y"
                                    class="filled-in chk-col-primary" />
                                <label for="posisi">
                                    <h5 class="text-bold">Posisi</h5>
                                </label>
                                <hr>

                                <input type="checkbox" id="organisasi" name="organisasi" value="Y"
                                    class="filled-in chk-col-primary" />
                                <label for="organisasi">
                                    <h5 class="text-bold">Organisasi</h5>
                                </label>
                                <hr>

                                <input type="checkbox" id="divisi" name="divisi" value="Y"
                                    class="filled-in chk-col-primary" />
                                <label for="divisi">
                                    <h5 class="text-bold">Divisi</h5>
                                </label>
                                <hr>

                                <input type="checkbox" id="departemen" name="departemen" value="Y"
                                    class="filled-in chk-col-primary" />
                                <label for="departemen">
                                    <h5 class="text-bold">Departemen</h5>
                                </label>
                                <hr>

                                <input type="checkbox" id="seksi" name="seksi" value="Y"
                                    class="filled-in chk-col-primary" />
                                <label for="seksi">
                                    <h5 class="text-bold">Seksi</h5>
                                </label>
                                <hr>

                                <input type="checkbox" id="grup" name="grup" value="Y"
                                    class="filled-in chk-col-primary" />
                                <label for="grup">
                                    <h5 class="text-bold">Grup</h5>
                                </label>
                                <hr>

                                <input type="checkbox" id="jabatan" name="jabatan" value="Y"
                                    class="filled-in chk-col-primary" />
                                <label for="jabatan">
                                    <h5 class="text-bold">Jabatan</h5>
                                </label>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-success">Export</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-6 col-12">
                            <form action="{{ route('master-data.export.kontrak') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <h5 class="text-bold">Kontrak <br><small>Note : Abaikan seluruh filter jika ingin
                                        mengeksport semua data.</small></h5>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-lg-6 col-12">
                                            <label for="">Mulai</label>
                                            <input type="month" name="kontrak_from" class="form-control">
                                        </div>
                                        <div class="col-lg-6 col-12">
                                            <label for="">Selesai</label>
                                            <input type="month" name="kontrak_to" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Durasi</label>
                                    <input type="number" name="durasi" class="form-control">
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-success">Export</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
