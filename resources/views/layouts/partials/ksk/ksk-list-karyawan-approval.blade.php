@if ($datas->isNotEmpty())
    @foreach ($datas as $i => $item)
        <div class="panel p-4 mb-3">
            <div class="panel-heading" id="approval-ksk-{{ $i }}" role="tab">
                <a class="panel-title" aria-controls="approval-ksk-content-{{ $i }}" aria-expanded="true"
                    data-bs-toggle="collapse" href="#approval-ksk-content-{{ $i }}"
                    data-parent="#list-approval-ksk">
                    <div class="row d-flex justify-content-between">
                        <div class="col flex-col">
                            <h5>{{ $item->nama_karyawan }}<br><small class="mt-0">{{ $item->ni_karyawan }}</small>
                            </h5>
                        </div>
                    </div>
                </a>
            </div>
            <div class="panel-collapse collapse mt-2 show" id="approval-ksk-content-{{ $i }}"
                aria-labelledby="approval-ksk-{{ $i }}" role="tabpanel"
                data-bs-parent="#approval-ksk-{{ $i }}">
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
                                <small class="text-muted">Kontrak Berjalan</small><br>
                                <p>{{ $item->karyawan->jenis_kontrak }}</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Surat Peringatan</small><br>
                                <input type="hidden" name="id_ksk_detail[]" id="id_ksk_detail{{ $i }}"
                                    class="form-control" value="{{ $item->id_ksk_detail }}">
                                <input type="number" name="jumlah_surat_peringatan"
                                    id="jumlah_surat_peringatan{{ $i }}" class="form-control"
                                    value="{{ $item->jumlah_surat_peringatan }}" min="0" disabled>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Sakit</small><br>
                                <input type="number" name="jumlah_sakit" id="jumlah_sakit{{ $i }}"
                                    class="form-control" value="{{ $item->jumlah_sakit }}" min="0" disabled>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Izin</small><br>
                                <input type="number" name="jumlah_izin" id="jumlah_izin{{ $i }}"
                                    class="form-control" value="{{ $item->jumlah_izin }}" min="0" disabled>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Alpa</small><br>
                                <input type="number" name="jumlah_alpa" id="jumlah_alpa{{ $i }}"
                                    class="form-control" value="{{ $item->jumlah_alpa }}" min="0" disabled>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label for="awal_{{ $i }}"><small class="text-muted">Tanggal Perjanjian
                                        Awal</small></label>
                                <input type="date" name="awal" id="awal_{{ $i }}" class="form-control"
                                    style="width: 100%;"
                                    value="{{ $item->karyawan->kontrak()->where('status', 'DONE')->orderByDesc('tanggal_mulai')->first()->tanggal_mulai }}"
                                    disabled>
                                </input>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label for="akhir_{{ $i }}"><small class="text-muted">Tanggal Perjanjian
                                        Akhir</small></label>
                                <input type="date" name="akhir" id="akhir_{{ $i }}"
                                    class="form-control" style="width: 100%;"
                                    value="{{ $item->karyawan->kontrak()->where('status', 'DONE')->orderByDesc('tanggal_selesai')->first()->tanggal_selesai }}"
                                    disabled>
                                </input>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Status KSK <span class="text-danger">*</span></small><br>
                                <select name="status_ksk[]" id="status_ksk{{ $i }}"
                                    class="form-control select2" data-id="{{ $i }}" style="width: 100%;"
                                    required>
                                    <option value="">Pilih Status KSK</option>
                                    <option value="PPJ" {{ $item->status_ksk == 'PPJ' ? 'selected' : '' }}>
                                        PERPANJANG (PKWT)
                                    </option>
                                    <option value="PPJMG" {{ $item->status_ksk == 'PPJMG' ? 'selected' : '' }}>
                                        PERPANJANG (MAGANG)
                                    </option>
                                    <option value="PHK" {{ $item->status_ksk == 'PHK' ? 'selected' : '' }}>PHK
                                    </option>
                                    <option value="TTP" {{ $item->status_ksk == 'TTP' ? 'selected' : '' }}>KARYAWAN
                                        TETAP</option>
                                </select>
                                <strong class="text-danger mt-1">Kontrak Berjalan
                                    : {{ $item->karyawan->jenis_kontrak }}</strong><br>
                                <small class="text-muted mt-1">Last Update :
                                    {{ $item->changeHistoryKSK->isNotEmpty() ? $item->changeHistoryKSK->sortByDesc('created_at')->first()->changed_by : '-' }}</small>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Durasi Renewal</small><br>
                                <input type="number" name="durasi_renewal[]" id="durasi_renewal{{ $i }}"
                                    class="form-control" value="{{ $item->durasi_renewal }}" min="0">
                                <small class="text-muted mt-1">Last Update :
                                    {{ $item->changeHistoryKSK->isNotEmpty() ? $item->changeHistoryKSK->sortByDesc('created_at')->first()->changed_by : '-' }}</small>
                            </div>
                        </div>
                        <hr>
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <small class="text-muted">History Perubahan</small><br>
                                <div class="row" id="change_history_ksk_{{ $i }}">
                                    @if ($item->changeHistoryKSK->isNotEmpty())
                                        @foreach ($item->changeHistoryKSK->sortBy('created_at') as $history)
                                            <div class="col-6 col-lg-3">
                                                <p><strong>{{ $history->changed_by }}</strong><br>
                                                    @if ($history->status_ksk_after == 'PPJ')
                                                        <span class="badge badge-success">Perpanjang (PKWT)</span>
                                                    @elseif ($history->status_ksk_after == 'PPJMG')
                                                        <span class="badge badge-success">Perpanjang (MAGANG)</span>
                                                    @elseif ($history->status_ksk_after == 'TTP')
                                                        <span class="badge badge-primary">Karyawan Tetap</span>
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
                        </div>
                        <hr>
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <small class="text-muted">Alasan</small><br>
                                @role('atasan')
                                    <textarea name="alasan[]" id="alasan{{ $i }}" class="form-control" rows="3"
                                        placeholder="{{ $item->changeHistoryKSK->isNotEmpty() ? $item->changeHistoryKSK->sortByDesc('created_at')->first()?->reason : '-' }} by {{ $item->changeHistoryKSK->isNotEmpty() ? $item->changeHistoryKSK->sortByDesc('created_at')->first()->changed_by : '-' }}">{{ $item->changeHistoryKSK->isNotEmpty() ? $item->changeHistoryKSK->where('changed_by_id', auth()->user()->karyawan->id_karyawan)->first()?->reason : '' }}</textarea>
                                    @elserole('personalia')
                                    <textarea name="alasan[]" id="alasan{{ $i }}" class="form-control" rows="3" disabled>{{ $item->changeHistoryKSK->isNotEmpty() ? $item->changeHistoryKSK->sortByDesc('created_at')->first()?->reason : '-' }} by {{ $item->changeHistoryKSK->isNotEmpty() ? $item->changeHistoryKSK->sortByDesc('created_at')->first()->changed_by : '-' }}</textarea>
                                @endrole
                            </div>
                        </div>
                        <hr>
                        <div class="col-12 col-lg-12">
                            <div id="previewAttachments_{{ $i }}">
                                @if ($item->attachments)
                                    @foreach ($item->attachments as $index => $attach)
                                        <a id="attachmentPreview_{{ $index }}"
                                            href="{{ asset('storage/' . $attach->path) }}"
                                            data-title="Attachment Ke-{{ $index }}" target="_blank">
                                            <img src="{{ asset('img/pdf-img.png') }}" alt="Attachment"
                                                style="width: 3.5rem;height: 3.5rem;" class="p-0">
                                        </a>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </div>
                            @if (in_array($item->ksk->parent_id, auth()->user()->karyawan->posisi->pluck('id_posisi')->toArray()))
                                <div class="form-group">
                                    <small class="text-muted">Attachment</small><br>
                                    @role('atasan')
                                        <input type="file" name="attachment" id="attachment{{ $i }}"
                                            class="form-control attachment"
                                            data-id-ksk-detail="{{ $item->id_ksk_detail }}"
                                            data-id="{{ $i }}" accept=".pdf">
                                        <small class="text-muted mt-1">Last Update :
                                            {{ $item->changeHistoryKSK->isNotEmpty() ? $item->changeHistoryKSK->sortByDesc('created_at')->first()->changed_by : '-' }}</small>
                                    @endrole
                                </div>
                            @endif
                        </div>
                        <div class="col-6 col-lg-6">
                            <small class="text-muted">History Kontrak</small><br>
                        </div>
                        <hr>
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
                                                <p>{{ $kontrak->jenis . ' ' . $no ?? '-' }}</p>
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
                        <hr>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btnUpdate" data-id="{{ $i }}"
                                data-id-ksk-detail="{{ $item->id_ksk_detail }}">
                                </i> Save Change
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
