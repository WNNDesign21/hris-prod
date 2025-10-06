@extends('layouts.auth-layout', ['page' => 'masterdata-kontrak'])

@section('title')
    {{ $pageTitle }}
@endsection

@section('header')
    @include('layouts.header')
@endsection

@section('navbar')
    @include('layouts.navbar-masterdata', ['page' => 'masterdata-kontrak'])
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header">
                    <h4 class="box-title">Edit Kontrak</h4>
                </div>
                <div class="box-body">
                    <form action="{{ route('master-data.kontrak.update', $kontrak->id_kontrak) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="row p-4">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="">ID Kontrak</label>
                                    <input type="text" name="id_kontrak" id="id_kontrak" class="form-control"
                                        value="{{ $kontrak->id_kontrak }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="">Nama Karyawan</label>
                                    <input type="text" name="nama_karyawan" id="nama_karyawan" class="form-control"
                                        value="{{ $kontrak->karyawan->nama }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="">No. Surat</label>
                                    <input type="text" name="no_surat" id="no_surat" class="form-control"
                                        value="{{ $kontrak->no_surat }}" placeholder="Contoh : 001">
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Dibuat</label>
                                    <input type="date" name="issued_date" id="issued_date" class="form-control"
                                        value="{{ $kontrak->issued_date }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Tempat</label>
                                    <select name="tempat_administrasi" id="tempat_administrasi" class="form-control" style="width: 100%;">
                                        <option value="Karawang" {{ $kontrak->tempat_administrasi == 'Karawang' ? 'selected' : '' }}>Karawang</option>
                                        <option value="Purwakarta" {{ $kontrak->tempat_administrasi == 'Purwakarta' ? 'selected' : '' }}>Purwakarta</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Jenis Perjanjian Kerja <span class="text-danger">*</span></label>
                                    <select name="jenis_kontrak" id="jenis_kontrak" class="form-control" style="width: 100%;">
                                        @foreach($jenisKontrak as $jk)
                                            <option value="{{ $jk->id }}" {{ $kontrak->jenis_kontrak_id == $jk->id ? 'selected' : '' }}>{{ $jk->nama_jenis_kontrak }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label class="form-label">Posisi <span class="text-danger">*</span></label>
                                    <select class="form-control" id="posisi" name="posisi" style="width: 100%;">
                                        @foreach($posisi as $p)
                                            <option value="{{ $p->id_posisi }}" {{ $kontrak->posisi->id_posisi == $p->id_posisi ? 'selected' : '' }}>{{ $p->nama_posisi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label for="">Nama Posisi</label>
                                    <br>
                                    <small>Note : Jika mengisi ini, maka nama posisi ini yang akan muncul di Template Kontrak, namun jika kosong, maka akan mengikuti bawaan dari Master Data Posisi</small>
                                    <input type="text" name="nama_posisi" id="nama_posisi" class="form-control"
                                        value="{{ $kontrak->nama_posisi }}"
                                        placeholder="Note : Abaikan jika mengikuti master data posisi">
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label for="">Durasi (Dalam Bulan)</label>
                                    <input type="number" name="durasi" id="durasi" class="form-control"
                                        value="{{ $kontrak->durasi }}"
                                        placeholder="Note : Abaikan jika memilih PKWTT/PENGKARYAAN">
                                </div>
                                <div class="form-group">
                                    <label for="">Salary</label>
                                    <input type="text" name="salary" id="salary" class="form-control"
                                        value="{{ $kontrak->salary }}">
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control"
                                        value="{{ $kontrak->tanggal_mulai }}">
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control"
                                        value="{{ $kontrak->tanggal_selesai }}">
                                </div>
                                <div class="form-group">
                                    <label for="">Deskripsi</label>
                                    <textarea name="deskripsi" id="deskripsi" class="form-control">{{ $kontrak->deskripsi }}</textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                <a href="{{ route('master-data.kontrak') }}" class="btn btn-secondary me-2"><i class="fas fa-arrow-left"></i> Kembali</a>
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
