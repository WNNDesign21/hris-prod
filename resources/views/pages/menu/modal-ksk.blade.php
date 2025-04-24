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
                                                    <span class="badge badge-success">Perpanjang (PKWT)</span>
                                                @elseif ($item->status_ksk == 'PPJMG')
                                                    <span class="badge badge-success">Perpanjang (MAGANG)</span>
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
                                            <hr>
                                            @if ($item->kontrak)
                                                <h4 class="box-title">Kontrak Baru</h4>
                                            @elseif ($item->cleareance)
                                                <h4 class="box-title">Exit Employee Clearance</h4>
                                            @endif
                                            <div class="row">
                                                @if ($item->kontrak)
                                                    <div class="col-6 col-lg-2">
                                                        <div class="form-group">
                                                            <small class="text-muted">ID Kontrak</small><br>
                                                            <p>{{ $item->kontrak->id_kontrak }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-2">
                                                        <div class="form-group">
                                                            <small class="text-muted">No Surat</small><br>
                                                            <p>
                                                                {{ $item->kontrak->no_surat }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-2">
                                                        <div class="form-group">
                                                            <small class="text-muted">Tanggal Dibuat</small><br>
                                                            <p>
                                                                {{ $item->kontrak->issued_date }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-2">
                                                        <div class="form-group">
                                                            <small class="text-muted">Posisi</small><br>
                                                            <p>
                                                                {{ $item->kontrak->nama_posisi ? $item->kontrak->nama_posisi : $item->kontrak->posisi->nama }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-2">
                                                        <div class="form-group">
                                                            <small class="text-muted">Tempat Administrasi</small><br>
                                                            <p>
                                                                {{ $item->kontrak->tempat_administrasi }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-2">
                                                        <div class="form-group">
                                                            <small class="text-muted">Jenis Kontrak</small><br>
                                                            <p>
                                                                {{ $item->kontrak->jenis }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-2">
                                                        <div class="form-group">
                                                            <small class="text-muted">Status</small><br>
                                                            @if ($item->kontrak->status == 'ON PROGRESS')
                                                                <span
                                                                    class="badge badge-warning">{{ $item->kontrak->status }}</span>
                                                            @else
                                                                <span
                                                                    class="badge badge-success">{{ $item->kontrak->status }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-2">
                                                        <div class="form-group">
                                                            <small class="text-muted">Durasi</small><br>
                                                            <p>
                                                                {{ $item->kontrak->durasi }} Bulan
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-2">
                                                        <div class="form-group">
                                                            <small class="text-muted">Salary</small><br>
                                                            <p>
                                                                Rp
                                                                {{ number_format($item->kontrak->salary, 0, ',', '.') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-2">
                                                        <div class="form-group">
                                                            <small class="text-muted">Deskripsi</small><br>
                                                            <p>
                                                                {{ $item->kontrak->deskripsi }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-2">
                                                        <div class="form-group">
                                                            <small class="text-muted">Tanggal Mulai</small><br>
                                                            <p>
                                                                {{ \Carbon\Carbon::parse($item->kontrak->tanggal_mulai)->translatedFormat('d F Y') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-2">
                                                        <div class="form-group">
                                                            <small class="text-muted">Tanggal Selesai</small><br>
                                                            <p>
                                                                {{ \Carbon\Carbon::parse($item->kontrak->tanggal_selesai)->translatedFormat('d F Y') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @elseif ($item->cleareance)
                                                    <div class="col-6 col-lg-2">
                                                        <div class="form-group">
                                                            <small class="text-muted">ID Clearance</small><br>
                                                            <p>
                                                                {{ $item->cleareance->id_cleareance }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    @if ($item->cleareance)
                                                        <div class="col-12">
                                                            <div class="table-responsive">
                                                                <table id="detail-cleareance-table"
                                                                    class="table border-primary b-1 table-bordered"
                                                                    style="width:100%">
                                                                    <thead class="bg-primary">
                                                                        <tr>
                                                                            <th>Departemen</th>
                                                                            <th>Deskripsi</th>
                                                                            <th>Status</th>
                                                                            <th>Keterangan</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($item->cleareance->cleareanceDetail as $item)
                                                                            <tr
                                                                                class="{{ !$item->confirmed_by ? 'bg-danger' : '' }}">
                                                                                <td style="width: 25%">
                                                                                    @if ($item->type == 'AL')
                                                                                        <p><strong>Atasan
                                                                                                Langsung</strong><br>
                                                                                        @elseif($item->type == 'IT')
                                                                                        <p><strong>Dept.IT</strong><br>
                                                                                        @elseif($item->type == 'FAT')
                                                                                        <p><strong>Dept.Finance</strong><br>
                                                                                        @elseif($item->type == 'GA')
                                                                                        <p><strong>Dept.GA</strong><br>
                                                                                        @elseif($item->type == 'HR')
                                                                                        <p><strong>Dept.HR</strong><br>
                                                                                    @endif
                                                                                    {{ $item?->karyawan?->nama ?? '-' }}
                                                                                    </p>
                                                                                </td>
                                                                                <td style="width: 25%">
                                                                                    @if ($item->type == 'AL')
                                                                                        <p><strong>Deskripsi Atasan
                                                                                                Langsung</strong></p>
                                                                                    @elseif($item->type == 'HR')
                                                                                        <p><strong>Deskripsi HR</strong>
                                                                                        </p>
                                                                                    @elseif($item->type == 'IT')
                                                                                        <p><strong>Deskripsi IT</strong>
                                                                                        </p>
                                                                                    @elseif($item->type == 'GA')
                                                                                        <p><strong>Deskripsi GA</strong>
                                                                                        </p>
                                                                                    @elseif($item->type == 'FAT')
                                                                                        <p><strong>Deskripsi
                                                                                                FAT</strong></p>
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
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
