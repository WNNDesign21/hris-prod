@foreach ($datas as $i => $item)
    <div class="col-12">
        <div class="box box-bordered border-info">
            <div class="box-body">
                <div class="row">
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <small class="text-muted">Karyawan</small><br>
                            <p>{{ $item->ni_karyawan }} - {{ $item->nama_karyawan }}</p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <small class="text-muted">Tanggal Bergabung</small><br>
                            <p>{{ \Carbon\Carbon::parse($item->karyawan->tanggal_mulai)->translatedFormat('d F Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <small class="text-muted">Jabatan</small><br>
                            <p>{{ $item->nama_jabatan }}</p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <small class="text-muted">Status</small><br>
                            <p>{{ $item->jenis_kontrak }}</p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="jumlah_surat_peringatan_{{ $i }}"><small class="text-muted">Surat
                                    Peringatan</small></label>
                            <input type="number" name="jumlah_surat_peringatan[]"
                                id="jumlah_surat_peringatan_{{ $i }}" min="0" class="form-control"
                                value="{{ $item->jumlah_surat_peringatan }}" style="width: 100%;" disabled>
                            </input>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="jumlah_sakit_{{ $i }}"><small
                                    class="text-muted">Sakit</small></label>
                            <input type="number" name="jumlah_sakit[]" id="jumlah_sakit_{{ $i }}"
                                min="0" class="form-control" style="width: 100%;"
                                value="{{ $item->jumlah_sakit }}" disabled>
                            </input>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="jumlah_izin_{{ $i }}"><small class="text-muted">Izin</small></label>
                            <input type="number" name="jumlah_izin[]" id="jumlah_izin_{{ $i }}"
                                min="0" class="form-control" style="width: 100%;"
                                value="{{ $item->jumlah_izin }}" disabled>
                            </input>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label for="jumlah_alpa_{{ $i }}"><small class="text-muted">Alpa</small></label>
                            <input type="number" name="jumlah_alpa[]" id="jumlah_alpa_{{ $i }}"
                                value="{{ $item->jumlah_alpa }}" min="0" class="form-control"
                                style="width: 100%;" value="0" disabled>
                            </input>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
