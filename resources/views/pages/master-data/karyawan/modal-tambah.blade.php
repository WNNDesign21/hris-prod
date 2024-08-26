<!-- modal Area -->
<div class="modal fade" id="modal-input-karyawan">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title p-4">Tambah Karyawan</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('master-data.karyawan.store') }}" method="POST"
                        enctype="multipart/form-data" id="form-tambah-karyawan">
                        @csrf
                        <div class="row p-4">
                            <div class="col-lg-6 col-12">
                                {{-- USER TAB --}}
                                <h5>Akun Karyawan</h5>
                                <ul class="nav nav-tabs customtab" role="tablist">
                                    <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab"
                                            href="#connect" role="tab"><span class="hidden-sm-up"><i
                                                    class="ion-person"></i></span>
                                            <span class="hidden-xs-down">HUBUNGKAN</span></a> </li>
                                    <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#create"
                                            role="tab"><span class="hidden-sm-up"><i class="ion-home"></i></span>
                                            <span class="hidden-xs-down">BUAT BARU</span></a> </li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div class="tab-pane active" id="connect" role="tabpanel">
                                        <div class="form-group">
                                            <label for="">Pilih User <span class="text-danger">*</span></label>
                                            <br>
                                            <div class="input-group mb-2">
                                                <select name="user_id" id="user_id" class="form-control"
                                                    style="width:100%;">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="create" role="tabpanel">
                                        <div class="form-group">
                                            <label for="">Email Akun <span class="text-danger">*</span></label>
                                            <br>
                                            <div class="input-group mb-2" style="width:100%;">
                                                <input type="email" name="email_akun" id="email_akun"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Username <span class="text-danger">*</span></label>
                                            <br>
                                            <div class="input-group mb-2" style="width:100%;">
                                                <input type="text" name="username" id="username"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Password <span class="text-danger">*</span></label>
                                            <br>
                                            <div class="input-group mb-2" style="width:100%;">
                                                <input type="password" name="password" id="password"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- END USER TAB --}}
                                <h5>Data Karyawan</h5>
                                <div class="form-group">
                                    <label for="">Nama <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="nama" id="nama" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">No. KTP</label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="no_ktp" id="no_ktp" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">NIK</label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="nik" id="nik" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Kota Lahir</label>
                                            <br>
                                            <div class="input-group mb-2" style="width:100%;">
                                                <input type="text" name="tempat_lahir" id="tempat_lahir"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Tanggal Lahir</label>
                                            <br>
                                            <div class="input-group mb-2" style="width:100%;">
                                                <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-control"
                                            style="width: 100%;" required>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Agama</label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <select name="agama" id="agama" class="form-control"
                                            style="width: 100%;">
                                            <option value="">Pilih Agama (Boleh Kosong)</option>
                                            <option value="ISLAM">Islam</option>
                                            <option value="KRISTEN">Kristen</option>
                                            <option value="PROTESTAN">Protestan</option>
                                            <option value="KONGHUCU">Konghucu</option>
                                            <option value="HINDU">Hindu</option>
                                            <option value="BUDHA">Budha</option>
                                            <option value="LAINNYA">Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Gol. Darah</label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <select name="gol_darah" id="gol_darah" class="form-control"
                                            style="width: 100%;">
                                            <option value="">Pilih Golongan Darah (Boleh Kosong)</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="AB">AB</option>
                                            <option value="O">O</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Status Keluarga</label>
                                    <br>
                                    <select name="status_keluarga" id="status_keluarga" class="form-control"
                                        style="width: 100%;">
                                        <option value="">Pilih Status Keluarga</option>
                                        <option value="MENIKAH">Menikah</option>
                                        <option value="LAJANG">Lajang</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Alamat</label>
                                    <br>
                                    <textarea name="alamat" id="alamat" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="">No. Telepon</label>
                                    <br>
                                    <input type="text" name="no_telp" id="no_telp" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Email</label>
                                    <br>
                                    <small>Note : Email ini untuk keperluan notifikasi sistem</small>
                                    <br>
                                    <input type="email" name="email" id="email" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">NPWP</label>
                                    <br>
                                    <input type="text" name="npwp" id="npwp" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. BPJS Kesehatan</label>
                                    <br>
                                    <input type="text" name="no_bpjs_ks" id="no_bpjs_ks" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. BPJS Ketenagakerjaan</label>
                                    <br>
                                    <input type="text" name="no_bpjs_kt" id="no_bpjs_kt" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Tahun Masuk <span class="text-danger">*</span></label>
                                    <br>
                                    <select name="tahun_masuk" id="tahun_masuk" class="form-control"
                                        style="width: 100%;" required>
                                        @php
                                            $currentYear = date('Y');
                                        @endphp
                                        @for ($year = 2017; $year <= $currentYear; $year++)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Status Karyawan <span
                                            class="text-danger">*</span></label>
                                    <select name="status_karyawan" id="status_karyawan" class="form-control"
                                        style="width: 100%;" disabled>
                                        <option value="AKTIF" selected>AKTIF</option>
                                        <option value="RESIGN">RESIGN</option>
                                        <option value="TERMINASI">TERMINASI</option>
                                        <option value="PENSIUN">PENSIUN</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Posisi <span class="text-danger">*</span></label>
                                    <select class="form-control" multiple="multiple" data-placeholder="Pilih Posisi"
                                        id="posisi" name="posisi[]" style="width: 100%;" required>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Grup <span class="text-danger">*</span></label>
                                    <select class="form-control" id="grup" name="grup" style="width: 100%;"
                                        required>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jatah Cuti <span class="text-danger">*</span></label>
                                    <input type="number" name="sisa_cuti" id="sisa_cuti" class="form-control"
                                        min='0' max='12' required>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                    Tambah</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
</div>
<!-- /.modal -->
