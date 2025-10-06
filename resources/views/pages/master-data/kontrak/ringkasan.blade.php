@extends('layouts.auth-layout')

@section('title')
    {{ $pageTitle }}
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#kontrak-ringkasan-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('masterdata.kontrak.ringkasan') }}',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'ni_karyawan' },
            { data: 'nama_karyawan' },
            { data: 'tanggal_masuk' },
            { data: 'dept' },
            { data: 'tanggal_penetapan_jabatan' },
            { data: 'tanggal_penetapan_kartap' },
            { data: 'lama_kerja' },
            { data: 'jenis_kontrak' },
            { data: 'jumlah_kontrak' },
            { data: 'tanggal_berakhir' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });
    $('.btnReload').on('click', function() {
        $('#kontrak-ringkasan-table').DataTable().ajax.reload();
    });
});
</script>
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
                    <h4 class="box-title">Detail Kontrak Karyawan</h4>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info waves-effect btnReload"><i
                                class="fas fa-sync-alt"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="kontrak-ringkasan-table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>NI Karyawan</th>
                                    <th>Nama Karyawan</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Dept</th>
                                    <th>Tgl Penetapan Jabatan</th>
                                    <th>Tgl Penetapan Kartap</th>
                                    <th>Lama Kerja</th>
                                    <th>Jenis Kontrak</th>
                                    <th>Jumlah Kontrak</th>
                                    <th>Tanggal Berakhir</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

