@if ($datas->isNotEmpty())
    @foreach ($datas as $i => $item)
        <div class="panel p-4 mb-3">
            <div class="panel-heading" id="detail-ksk-{{ $i }}" role="tab">
                <a class="panel-title" aria-controls="detail-ksk-content-{{ $i }}" aria-expanded="true"
                    data-bs-toggle="collapse" href="#detail-ksk-content-{{ $i }}"
                    data-parent="#list-detail-ksk">
                    <div class="row d-flex justify-content-between">
                        <div class="col flex-col">
                            <h5>{{ $item->nama_karyawan }}<br><small class="mt-0">{{ $item->ni_karyawan }}</small>
                            </h5>
                        </div>
                    </div>
                </a>
            </div>
            <div class="panel-collapse collapse mt-2" id="detail-ksk-content-{{ $i }}"
                aria-labelledby="detail-ksk-{{ $i }}" role="tabpanel"
                data-bs-parent="#detail-ksk-{{ $i }}">
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
                                <input type="hidden" name="id_ksk_detailEdit"
                                    id="id_ksk_detailEdit{{ $i }}" class="form-control"
                                    value="{{ $item->id_ksk_detail }}">
                                <input type="number" name="jumlah_surat_peringatanEdit"
                                    id="jumlah_surat_peringatanEdit{{ $i }}" class="form-control"
                                    value="{{ $item->jumlah_surat_peringatan }}" min="0">
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Sakit</small><br>
                                <input type="number" name="jumlah_sakitEdit" id="jumlah_sakitEdit{{ $i }}"
                                    class="form-control" value="{{ $item->jumlah_sakit }}" min="0">
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Izin</small><br>
                                <input type="number" name="jumlah_izinEdit" id="jumlah_izinEdit{{ $i }}"
                                    class="form-control" value="{{ $item->jumlah_izin }}" min="0">
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Alpa</small><br>
                                <input type="number" name="jumlah_alpaEdit" id="jumlah_alpaEdit{{ $i }}"
                                    class="form-control" value="{{ $item->jumlah_alpa }}" min="0">
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Status KSK</small><br>
                                @if ($item->status_ksk == 'PPJ')
                                    <span class="badge bg-success">PERPANJANG</span>
                                @elseif ($item->status_ksk == 'TTP')
                                    <span class="badge bg-primary">KARYAWAN TETAP</span>
                                @else
                                    <span class="badge bg-danger">PHK</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Tanggal Perjanjian</small><br>
                                <p>{{ $item->latest_kontrak_tanggal_mulai }} -
                                    {{ $item->latest_kontrak_tanggal_selesai }}
                                </p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Durasi Renewal</small><br>
                                <p>{{ $item->durasi_renewal ?? '-' }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="col-6 col-lg-6">
                            <small class="text-muted">History Kontrak</small><br>
                        </div>
                        @php
                            $no = 1;
                        @endphp
                        @if ($item->karyawan->kontrak)
                            @foreach ($item->karyawan->kontrak()->where('status', 'DONE')->orderByDesc('tanggal_selesai')->get() as $kontrak)
                                <div class="col-12">
                                    <div class="row  d-flex">
                                        <div class="col-6 col-lg-3">
                                            <div class="form-group">
                                                <small class="text-muted">Jenis</small><br>
                                                <p>{{ $no . '. ' . $kontrak->jenis ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-3">
                                            <div class="form-group">
                                                <small class="text-muted">ID Kontrak</small><br>
                                                <p>{{ $kontrak->id_kontrak ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-3">
                                            <div class="form-group">
                                                <small class="text-muted">Posisi</small><br>
                                                <p>{{ $kontrak->nama_posisi ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-3">
                                            <div class="form-group">
                                                <small class="text-muted">Periode</small><br>
                                                <p>{{ $kontrak->tanggal_mulai }} - {{ $kontrak->tanggal_selesai }}
                                                    ({{ $kontrak->durasi }} Bulan)
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $no++;
                                @endphp
                            @endforeach
                        @endif
                        @if ($item->reviewed_dir_by == null)
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-success btnUpdate"
                                    data-id="{{ $i }}">
                                    <i class="fas fa-save"></i> Update
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
