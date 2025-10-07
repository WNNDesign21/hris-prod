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
                <h4 class="box-title">Detail Kontrak Karyawan</h4>
                <a href="{{ route('master-data.kontrak-detail.index') }}" class="btn btn-secondary">Back</a>
            </div>
            <div class="box-body">
                <p><strong>NI Karyawan:</strong> {{ $karyawan->ni_karyawan }}</p>
                <p><strong>Nama Karyawan:</strong> {{ $karyawan->nama }}</p>
                <p><strong>Tanggal Masuk:</strong> {{ \Carbon\Carbon::parse($karyawan->tanggal_mulai)->format('d M Y') }}</p>
                <p><strong>Departemen:</strong> {{ $karyawan->posisi->isNotEmpty() ? $karyawan->posisi->first()->departemen->nama : '-' }}</p>
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kontrak Ke</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Rekomendasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kontraks as $index => $kontrak)
                            <tr>
                                <td>KONTRAK {{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($kontrak->tanggal_mulai)->format('d M Y') }}</td>
                                <td>{{ $kontrak->tanggal_selesai ? \Carbon\Carbon::parse($kontrak->tanggal_selesai)->format('d M Y') : '-' }}</td>
                                <td>{{ $kontrak->status_rekomendasi }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
