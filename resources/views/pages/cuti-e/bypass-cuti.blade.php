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
        <div class="col-12">
            <div class="box">
                <div class="box-header d-flex justify-content-between">
                    <div class="row">
                        <h4 class="box-title mb-2">Bypass Cuti</h4>
                        <small class="text-fade">Note : Fitur ini hanya digunakan untuk cuti pribadi,
                            <strong>Hanya</strong>
                            gunakan fitur ini jika ada kebutuhan khusus,
                            seperti cuti yang dadakan namun tetap harus terdata pada sistem, <strong>Gunakan menu ini dengan
                                bijak!</strong>
                        </small>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <form action="{{ route('cutie.bypass-cuti.store') }}" method="POST" enctype="multipart/form-data"
                            id="form-bypass-cuti">
                            @csrf
                            <div class="row p-4">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Karyawan</label>
                                        <select name="id_karyawan" id="id_karyawan" class="form-control"
                                            style="width: 100%;">
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Penggunaan Sisa Cuti</label>
                                        <select name="penggunaan_sisa_cuti" id="penggunaan_sisa_cuti" class="form-control"
                                            style="width: 100%;">
                                            <option value="TB">TAHUN BERJALAN ({{ date('Y') }})</option>
                                            <option value="TL">TAHUN LALU ({{ date('Y') - 1 }})</option>
                                        </select>
                                        <small class="text-fade">Note : Jika hendak cuti sebanyak 2 hari, dan memiliki sisa
                                            cuti tahun lalu
                                            hanya sebanyak 1 hari, maka jika tetap ingin mengambil 2 hari, karyawan harus
                                            membuat 2 dokumen cuti dengan memilih cuti tahun berjalan 1 hari, dan cuti tahun
                                            lalu 1 hari</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Rencana Mulai</label>
                                        <input type="date" name="rencana_mulai_cuti" id="rencana_mulai_cuti"
                                            class="form-control" required min="{{ date('Y-m-d', strtotime('-7 days')) }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Rencana Selesai</label>
                                        <input type="date" name="rencana_selesai_cuti" id="rencana_selesai_cuti"
                                            class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Alasan Cuti</label>
                                        <textarea class="form-control" name="alasan_cuti" id="alasan_cuti"
                                            placeholder="Jika Cuti Pribadi, Wajib diisi untuk pertimbangan atasan!" style="width: 100%;"></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end mt-2">
                                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                            Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
