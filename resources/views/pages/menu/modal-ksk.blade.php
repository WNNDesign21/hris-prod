<!-- modal Area -->
<div class="modal fade" id="modal-ksk">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header p-4">
                <h4 class="modal-title">My KSK</h4>
                <button type="button" class="btn-close btnCloseKSK" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="panel-group panel-group-simple panel-group-continuous mb-2" id="list-ksk"
                    aria-multiselectable="true" role="tablist">
                    @if (!empty($myKSK))
                        @foreach ($myKSK as $i => $item)
                            <div class="panel p-4 mb-3">
                                <div class="panel-heading" id="ksk-{{ $i }}" role="tab">
                                    <a class="panel-title" aria-controls="ksk-content-{{ $i }}"
                                        aria-expanded="true" data-bs-toggle="collapse"
                                        href="#ksk-content-{{ $i }}" data-parent="#list-ksk">
                                        <div class="d-flex justify-content-between">
                                            <h3>{{ $item->ksk_id }}<br><small>{{ \Carbon\Carbon::parse($item->ksk->release_date)->format('d F Y') }}
                                                    <small></h3>
                                            <div>
                                                @if ($item->status_ksk == 'PPJ')
                                                    <span class="badge badge-success">Perpanjang</span>
                                                @elseif ($item->status_ksk == 'TTP')
                                                    <span class="badge badge-primary">Karyawan
                                                        Tetap</span>
                                                @elseif ($item->status_ksk == 'PHK')
                                                    <span class="badge badge-danger">PHK</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="panel-collapse collapse mt-2" id="ksk-content-{{ $i }}"
                                    aria-labelledby="ksk-{{ $i }}" role="tabpanel"
                                    data-bs-parent="#ksk-{{ $i }}">
                                    <div class="panel-body">
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
                                                    <small class="text-muted">Surat Peringatan</small><br>
                                                    <input type="hidden" name="id_ksk_]" id="id_ksk_{ $i }}"
                                                        class="form-control" value="{{ $item->id_ksk_ }}">
                                                    <input type="number" name="jumlah_surat_peringatan"
                                                        id="jumlah_surat_peringatan{{ $i }}"
                                                        class="form-control"
                                                        value="{{ $item->jumlah_surat_peringatan }}" min="0"
                                                        disabled>
                                                </div>
                                            </div>
                                            <div class="col-6 col-lg-3">
                                                <div class="form-group">
                                                    <small class="text-muted">Sakit</small><br>
                                                    <input type="number" name="jumlah_sakit"
                                                        id="jumlah_sakit{{ $i }}" class="form-control"
                                                        value="{{ $item->jumlah_sakit }}" min="0" disabled>
                                                </div>
                                            </div>
                                            <div class="col-6 col-lg-3">
                                                <div class="form-group">
                                                    <small class="text-muted">Izin</small><br>
                                                    <input type="number" name="jumlah_izin"
                                                        id="jumlah_izin{{ $i }}" class="form-control"
                                                        value="{{ $item->jumlah_izin }}" min="0" disabled>
                                                </div>
                                            </div>
                                            <div class="col-6 col-lg-3">
                                                <div class="form-group">
                                                    <small class="text-muted">Alpa</small><br>
                                                    <input type="number" name="jumlah_alpa"
                                                        id="jumlah_alpa{{ $i }}" class="form-control"
                                                        value="{{ $item->jumlah_alpa }}" min="0" disabled>
                                                </div>
                                            </div>
                                            <div class="col-6 col-lg-3">
                                                <div class="form-group">
                                                    <label for="awal_{{ $i }}"><small
                                                            class="text-muted">Tanggal
                                                            Perjanjian
                                                            Awal</small></label>
                                                    <input type="date" name="awal" id="awal_{{ $i }}"
                                                        class="form-control" style="width: 100%;"
                                                        value="{{ $item->karyawan->kontrak()->where('status', 'DONE')->orderByDesc('tanggal_selesai')->first()->tanggal_mulai }}"
                                                        disabled>
                                                    </input>
                                                </div>
                                            </div>
                                            <div class="col-6 col-lg-3">
                                                <div class="form-group">
                                                    <label for="akhir_{{ $i }}"><small
                                                            class="text-muted">Tanggal
                                                            Perjanjian
                                                            Akhir</small></label>
                                                    <input type="date" name="akhir"
                                                        id="akhir_{{ $i }}" class="form-control"
                                                        style="width: 100%;"
                                                        value="{{ $item->karyawan->kontrak()->where('status', 'DONE')->orderByDesc('tanggal_selesai')->first()->tanggal_selesai }}"
                                                        disabled>
                                                    </input>
                                                </div>
                                            </div>
                                            <div class="col-6 col-lg-3">
                                                <div class="form-group">
                                                    <small class="text-muted">Status KSK <span
                                                            class="text-danger">*</span></small><br>
                                                    <select name="status_ksk[]" id="status_ksk{{ $i }}"
                                                        class="form-control select2" style="width: 100%;" disabled>
                                                        <option value="">Pilih Status KSK</option>
                                                        <option value="PPJ"
                                                            {{ $item->status_ksk == 'PPJ' ? 'selected' : '' }}>
                                                            PERPANJANG
                                                        </option>
                                                        <option value="PHK"
                                                            {{ $item->status_ksk == 'PHK' ? 'selected' : '' }}>
                                                            PHK</option>
                                                        <option value="TTP"
                                                            {{ $item->status_ksk == 'TTP' ? 'selected' : '' }}>
                                                            KARYAWAN
                                                            TETAP</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6 col-lg-3">
                                                <div class="form-group">
                                                    <small class="text-muted">Durasi Renewal</small><br>
                                                    <input type="text" name="durasi_renewal[]"
                                                        id="durasi_renewal{{ $i }}" class="form-control"
                                                        value="{{ $item->durasi_renewal }} Bulan" disabled>
                                                </div>
                                            </div>
                                            {{-- <div class="col-12 col-lg-12">
                                                <div class="form-group">
                                                    <small class="text-muted">History Perubahan</small><br>
                                                    <div class="row">
                                                        @if ($item->changeHistoryKSK->isNotEmpty())
                                                            @foreach ($item->changeHistoryKSK->sortBy('created_at') as $history)
                                                                <div class="col-6 col-lg-3">
                                                                    <p><strong>{{ $history->changed_by }}</strong><br>
                                                                        @if ($history->status_ksk_after == 'PPJ')
                                                                            <span
                                                                                class="badge badge-success">Perpanjang</span>
                                                                        @elseif ($history->status_ksk_after == 'TTP')
                                                                            <span class="badge badge-primary">Karyawan
                                                                                Tetap</span>
                                                                        @elseif ($history->status_ksk_after == 'PHK')
                                                                            <span class="badge badge-danger">PHK</span>
                                                                        @endif
                                                                        <br>
                                                                        {{ $history->durasi_after }} Bulan<br>

                                                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $history->created_at)->format('d F Y H:i') }}
                                                                        WIB <br>
                                                                        Alasan : {{ $history->reason }}<br>
                                                                    </p>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
