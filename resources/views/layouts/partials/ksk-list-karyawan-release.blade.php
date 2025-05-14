@if ($datas->isNotEmpty())
    @foreach ($datas as $i => $item)
        <div class="col-12">
            <div class="box box-bordered border-info">
                <div class="box-body">
                    <div class="row">
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Karyawan</small><br>
                                <p>{{ $item->ni_karyawan }} - {{ $item->nama }}</p>
                                <input type="hidden" name="id_karyawan[]" value="{{ $item->id_karyawan }}">
                                <input type="hidden" name="ni_karyawan[]" value="{{ $item->ni_karyawan }}">
                                <input type="hidden" name="nama_karyawan[]" value="{{ $item->nama }}">
                                <input type="hidden" name="jenis_kontrak[]" value="{{ $item->jenis_kontrak }}"
                                    id="jenis_kontrak_{{ $i }}">
                                <input type="hidden" name="posisi_id[]" value="{{ $item->id_posisi }}">
                                <input type="hidden" name="nama_posisi[]" value="{{ $item->nama_posisi }}">
                                <input type="hidden" name="jabatan_id[]" value="{{ $item->id_jabatan }}">
                                <input type="hidden" name="nama_jabatan[]" value="{{ $item->nama_jabatan }}">
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Tanggal Bergabung</small><br>
                                <p>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d F Y') }}</p>
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
                                <small class="text-muted">Kontrak Berjalan</small><br>
                                <strong class="text-danger">{{ $item->jenis_kontrak }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label for="jumlah_surat_peringatan_{{ $i }}"><small
                                        class="text-muted">Surat
                                        Peringatan</small></label>
                                <input type="number" name="jumlah_surat_peringatan[]"
                                    id="jumlah_surat_peringatan_{{ $i }}" min="0"
                                    class="form-control" value="0" style="width: 100%;" required>
                                </input>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label for="jumlah_sakit_{{ $i }}"><small
                                        class="text-muted">Sakit</small></label>
                                <input type="number" name="jumlah_sakit[]" id="jumlah_sakit_{{ $i }}"
                                    min="0" class="form-control" style="width: 100%;"
                                    value="{{ $item->total_sakit }}" required>
                                </input>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label for="jumlah_izin_{{ $i }}"><small
                                        class="text-muted">Izin</small></label>
                                <input type="number" name="jumlah_izin[]" id="jumlah_izin_{{ $i }}"
                                    min="0" class="form-control" style="width: 100%;"
                                    value="{{ $item->total_izin }}" required>
                                </input>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label for="jumlah_alpa_{{ $i }}"><small
                                        class="text-muted">Alpa</small></label>
                                <input type="number" name="jumlah_alpa[]" id="jumlah_alpa_{{ $i }}"
                                    min="0" class="form-control" style="width: 100%;" value="0" required>
                                </input>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label for="awal_{{ $i }}"><small class="text-muted">Awal</small></label>
                                <input type="date" name="awal[]" id="awal_{{ $i }}" class="form-control"
                                    style="width: 100%;" value="{{ $item->tanggal_mulai_kontrak_terakhir }}" disabled>
                                </input>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label for="akhir_{{ $i }}"><small class="text-muted">Akhir</small></label>
                                <input type="date" name="akhir[]" id="akhir_{{ $i }}"
                                    class="form-control" style="width: 100%;"
                                    value="{{ $item->tanggal_selesai_kontrak_terakhir }}" disabled>
                                </input>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
