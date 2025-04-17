<div class="form-group">
    <div class="col-12">
        <div class="box p-4">
            <div class="box-body">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center text-center mb-3">
                        <h1 class="box-title">KONFIRMASI STATUS KARYAWAN</h4>
                    </div>
                </div>
                <div class="row">
                    <p>Divisi : {{ $detail_ksk->ksk->nama_divisi }}</p>
                    <p>Departemen : {{ $detail_ksk->ksk->nama_departemen }}</p>
                    <p>Release Date : {{ $detail_ksk->ksk->release_date }}</p>
                </div>
                <hr>
                <h4 class="box-title">Detail KSK</h4>
                <div class="row">
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <small class="text-muted">Leader</small><br>
                            <p>
                                ✅{{ $detail_ksk->ksk->released_by }}<br>
                                <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $detail_ksk->ksk->released_at)->format('d F Y H:i') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <small class="text-muted">Section Head</small><br>
                            <p>
                                ✅{{ $detail_ksk->ksk->checked_by }}<br>
                                <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $detail_ksk->ksk->checked_at)->format('d F Y H:i') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <small class="text-muted">Dept.Head</small><br>
                            <p>
                                ✅{{ $detail_ksk->ksk->approved_by }}<br>
                                <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $detail_ksk->ksk->approved_at)->format('d F Y H:i') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <small class="text-muted">Div.Head</small><br>
                            <p>
                                ✅{{ $detail_ksk->ksk->reviewed_div_by }}<br>
                                <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $detail_ksk->ksk->reviewed_div_at)->format('d F Y H:i') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <small class="text-muted">Plant Head</small><br>
                            <p>
                                ✅{{ $detail_ksk->ksk->reviewed_ph_by }}<br>
                                <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $detail_ksk->ksk->reviewed_ph_at)->format('d F Y H:i') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <small class="text-muted">Director</small><br>
                            <p>
                                ✅{{ $detail_ksk->ksk->reviewed_dir_by }}<br>
                                <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $detail_ksk->ksk->reviewed_dir_at)->format('d F Y H:i') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="row">
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Karyawan</small><br>
                                <p>{{ $detail_ksk->ni_karyawan }} - {{ $detail_ksk->nama_karyawan }}</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Tanggal Bergabung</small><br>
                                <p>{{ \Carbon\Carbon::parse($detail_ksk->karyawan->tanggal_mulai)->translatedFormat('d F Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Jabatan</small><br>
                                <p>{{ $detail_ksk->nama_jabatan }}</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Status</small><br>
                                <p>{{ $detail_ksk->jenis_kontrak }}</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Surat Peringatan</small><br>
                                <input type="number" name="jumlah_surat_peringatan" id="jumlah_surat_peringatan"
                                    class="form-control" value="{{ $detail_ksk->jumlah_surat_peringatan }}"
                                    min="0" disabled>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Sakit</small><br>
                                <input type="number" name="jumlah_sakit" id="jumlah_sakit" class="form-control"
                                    value="{{ $detail_ksk->jumlah_sakit }}" min="0" disabled>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Izin</small><br>
                                <input type="number" name="jumlah_izin" id="jumlah_izin" class="form-control"
                                    value="{{ $detail_ksk->jumlah_izin }}" min="0" disabled>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Alpa</small><br>
                                <input type="number" name="jumlah_alpa" id="jumlah_alpa" class="form-control"
                                    value="{{ $detail_ksk->jumlah_alpa }}" min="0" disabled>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label for="awal_"><small class="text-muted">Tanggal Perjanjian
                                        Awal</small></label>
                                <input type="date" name="awal" id="awal_" class="form-control"
                                    style="width: 100%;"
                                    value="{{ $detail_ksk->tanggal_renewal_kontrak ? $detail_ksk->karyawan->kontrak->where('tanggal_selesai', '<', $detail_ksk->tanggal_renewal_kontrak)->sortByDesc('tanggal_selesai')->first()->tanggal_mulai : $detail_ksk->karyawan->kontrak->sortByDesc('tanggal_selesai')->first()->tanggal_mulai ?? '' }}"
                                    disabled>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <label for="akhir_"><small class="text-muted">Tanggal Perjanjian
                                        Akhir</small></label>
                                <input type="date" name="akhir" id="akhir_" class="form-control"
                                    style="width: 100%;"
                                    value="{{ $detail_ksk->tanggal_renewal_kontrak ? $detail_ksk->karyawan->kontrak->where('tanggal_selesai', '<', $detail_ksk->tanggal_renewal_kontrak)->sortByDesc('tanggal_selesai')->first()->tanggal_selesai : $detail_ksk->karyawan->kontrak->sortByDesc('tanggal_selesai')->first()->tanggal_selesai ?? '' }}"
                                    disabled>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Status KSK <span class="text-danger">*</span></small><br>
                                <select name="status_ksk[]" id="status_ksk" class="form-control select2"
                                    style="width: 100%;" disabled>
                                    <option value="">Pilih Status KSK</option>
                                    <option value="PPJ" {{ $detail_ksk->status_ksk == 'PPJ' ? 'selected' : '' }}>
                                        PERPANJANG
                                    </option>
                                    <option value="PHK" {{ $detail_ksk->status_ksk == 'PHK' ? 'selected' : '' }}>
                                        PHK
                                    </option>
                                    <option value="TTP" {{ $detail_ksk->status_ksk == 'TTP' ? 'selected' : '' }}>
                                        KARYAWAN
                                        TETAP</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-group">
                                <small class="text-muted">Durasi Renewal</small><br>
                                <input type="number" name="durasi_renewal[]" id="durasi_renewal"
                                    class="form-control" value="{{ $detail_ksk->durasi_renewal }}" min="0"
                                    disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <h4 class="box-title">History Perubahan</h4>
                <div class="row">
                    <div class="form-group">
                        <div class="row">
                            @if ($detail_ksk->changeHistoryKSK->isNotEmpty())
                                @foreach ($detail_ksk->changeHistoryKSK->sortBy('created_at') as $history)
                                    <div class="col-6 col-lg-3">
                                        <p>
                                            @if ($history->status_ksk_after == 'PPJ')
                                                <span class="badge badge-success">Perpanjang</span>
                                            @elseif ($history->status_ksk_after == 'TTP')
                                                <span class="badge badge-primary">Karyawan Tetap</span>
                                            @elseif ($history->status_ksk_after == 'PHK')
                                                <span class="badge badge-danger">PHK</span>
                                            @endif
                                            <br>
                                            {{ $history->changed_by }}
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
                @if ($detail_ksk->kontrak)
                    <h4 class="box-title">Kontrak Baru</h4>
                @else
                    <h4 class="box-title">Exit Employee Clearance</h4>
                @endif
                <div class="row">
                    @if ($detail_ksk->kontrak)
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <small class="text-muted">ID Kontrak</small><br>
                                <p>{{ $detail_ksk->kontrak->id_kontrak }}</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <small class="text-muted">No Surat</small><br>
                                <p>
                                    {{ $detail_ksk->kontrak->no_surat }}
                                </p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <small class="text-muted">Tanggal Dibuat</small><br>
                                <p>
                                    {{ $detail_ksk->kontrak->issued_date }}
                                </p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <small class="text-muted">Posisi</small><br>
                                <p>
                                    {{ $detail_ksk->kontrak->nama_posisi ? $detail_ksk->kontrak->nama_posisi : $detail_ksk->kontrak->posisi->nama }}
                                </p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <small class="text-muted">Tempat Administrasi</small><br>
                                <p>
                                    {{ $detail_ksk->kontrak->tempat_administrasi }}
                                </p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <small class="text-muted">Jenis Kontrak</small><br>
                                <p>
                                    {{ $detail_ksk->kontrak->jenis }}
                                </p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <small class="text-muted">Status</small><br>
                                @if ($detail_ksk->kontrak->status == 'ON PROGRESS')
                                    <span class="badge badge-warning">{{ $detail_ksk->kontrak->status }}</span>
                                @else
                                    <span class="badge badge-success">{{ $detail_ksk->kontrak->status }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <small class="text-muted">Durasi</small><br>
                                <p>
                                    {{ $detail_ksk->kontrak->durasi }} Bulan
                                </p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <small class="text-muted">Salary</small><br>
                                <p>
                                    Rp {{ number_format($detail_ksk->kontrak->salary, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <small class="text-muted">Deskripsi</small><br>
                                <p>
                                    {{ $detail_ksk->kontrak->deskripsi }}
                                </p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <small class="text-muted">Tanggal Mulai</small><br>
                                <p>
                                    {{ \Carbon\Carbon::parse($detail_ksk->kontrak->tanggal_mulai)->translatedFormat('d F Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <small class="text-muted">Tanggal Selesai</small><br>
                                <p>
                                    {{ \Carbon\Carbon::parse($detail_ksk->kontrak->tanggal_selesai)->translatedFormat('d F Y') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="col-6 col-lg-2">
                            <div class="form-group">
                                <small class="text-muted">ID Clearance</small><br>
                                <p>
                                    {{ $detail_ksk->cleareance->id_cleareance }}
                                </p>
                            </div>
                        </div>
                        @if ($detail_ksk->cleareance)
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table id="detail-cleareance-table"
                                        class="table border-primary b-1 table-bordered" style="width:100%">
                                        <thead class="bg-primary">
                                            <tr>
                                                <th>Departemen</th>
                                                <th>Deskripsi</th>
                                                <th>Status</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($detail_ksk->cleareance->cleareanceDetail as $item)
                                                <tr>
                                                    <td style="width: 25%">
                                                        @if ($item->type == 'AL')
                                                            <p><strong>Atasan Langsung</strong><br>
                                                            @elseif ($item->type == 'IT')
                                                            <p><strong>Dept.IT</strong><br>
                                                            @elseif ($item->type == 'FAT')
                                                            <p><strong>Dept.Finance</strong><br>
                                                            @elseif ($item->type == 'GA')
                                                            <p><strong>Dept.GA</strong><br>
                                                            @elseif ($item->type == 'HR')
                                                            <p><strong>Dept.HR</strong><br>
                                                        @endif
                                                        {{ $item?->karyawan?->nama ?? '-' }}</p>
                                                    </td>
                                                    <td style="width: 25%">
                                                        @if ($item->type == 'AL')
                                                            <p><strong>Deskripsi Atasan Langsung</strong></p>
                                                        @elseif ($item->type == 'HR')
                                                            <p><strong>Deskripsi HR</strong></p>
                                                        @elseif ($item->type == 'IT')
                                                            <p><strong>Deskripsi IT</strong></p>
                                                        @elseif ($item->type == 'GA')
                                                            <p><strong>Deskripsi GA</strong></p>
                                                        @elseif ($item->type == 'FAT')
                                                            <p><strong>Deskripsi FAT</strong></p>
                                                        @endif
                                                    </td>
                                                    <td style="width: 25%">
                                                        @if ($item->is_clear == 'Y')
                                                            <p>
                                                                ✅{{ $item->confirmed_by }}<br>
                                                                <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->confirmed_at)->format('d F Y H:i') }}</span>
                                                            </p>
                                                        @else
                                                            <p>
                                                                @if ($item->confirmed_by)
                                                                    ❌{{ $item->confirmed_by }}<br>
                                                                    <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->confirmed_at)->format('d F Y H:i') }}</span>
                                                                @else
                                                                    <p>⏳ Waiting</p>
                                                                @endif
                                                            </p>
                                                        @endif
                                                    </td>
                                                    <td style="width: 25%">
                                                        {{ $item->keterangan }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
