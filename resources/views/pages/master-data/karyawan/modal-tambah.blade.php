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
                                    <a id="link_foto" href="{{ asset('img/no-image.png') }}" data-title="Foto Karyawan"
                                        class="image-popup-vertical-fit">
                                        <img id="image_review" src="{{ asset('img/no-image.png') }}" alt="Foto Karyawan"
                                            style="width: 150px;height: 150px;" class="img-thumbnail img-fluid">
                                    </a>
                                </div>
                                <div class="form-group">
                                    <label for="">Foto</label>
                                    <input type="file" name="foto" type="file" id="foto"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Nomor Induk Karyawan <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="ni_karyawan" id="ni_karyawan"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Nama <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="nama" id="nama" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">No. KK <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="no_kk" id="no_kk" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">NIK <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="nik" id="nik" class="form-control"
                                            required>
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
                                            <option value="L">LAKI-LAKI</option>
                                            <option value="P">PEREMPUAN</option>
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
                                            <option value="ISLAM">ISLAM</option>
                                            <option value="KRISTEN">KRISTEN</option>
                                            <option value="KATOLIK">KATOLIK</option>
                                            <option value="PROTESTAN">PROTESTAN</option>
                                            <option value="KONGHUCU">KONGHUCU</option>
                                            <option value="HINDU">HINDU</option>
                                            <option value="BUDHA">BUDHA</option>
                                            <option value="LAINNYA">LAINNYA</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Gol. Darah <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <select name="gol_darah" id="gol_darah" class="form-control"
                                            style="width: 100%;" required>
                                            <option value="">Pilih Golongan Darah (Boleh Kosong)</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="AB">AB</option>
                                            <option value="O">O</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Status Keluarga <span class="text-danger">*</span></label>
                                    <br>
                                    <select name="status_keluarga" id="status_keluarga" class="form-control"
                                        style="width: 100%;" required>
                                        <option value="">Pilih Status Keluarga</option>
                                        <option value="MENIKAH">MENIKAH</option>
                                        <option value="BELUM MENIKAH">BELUM MENIKAH</option>
                                        <option value="CERAI">CERAI</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="kategori_keluarga">Kategori Keluarga</label>
                                    <select class="form-control" id="kategori_keluarga" name="kategori_keluarga">
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="TK/0">TK/0</option>
                                        <option value="TK/1">TK/1</option>
                                        <option value="TK/2">TK/2</option>
                                        <option value="TK/3">TK/3</option>
                                        <option value="K/0">K/0</option>
                                        <option value="K/1">K/1</option>
                                        <option value="K/2">K/2</option>
                                        <option value="K/3">K/3</option>
                                    </select>
                                </div>
                                {{-- AWAL BLOK DATA KELUARGA --}}
                                <div class="form-group">
                                    <label for="status_kawin">Status Kawin <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status_kawin" name="status_kawin" required>
                                        <option value="">Pilih Status</option>
                                        <option value="BK">Belum Kawin (BK)</option>
                                        <option value="K">Kawin (K)</option>
                                        <option value="KA1">Kawin Anak 1 (KA1)</option>
                                        <option value="KA2">Kawin Anak 2 (KA2)</option>
                                        <option value="KA3">Kawin Anak 3 (KA3)</option>
                                        <option value="KA4">Kawin Anak 4 (KA4)</option>
                                        <option value="KA5">Kawin Anak 5 (KA5)</option>
                                    </select>
                                </div>

                                <hr>
                                <h5>Data Anggota Keluarga</h5>
                                <div id="keluarga-wrapper">
                                    {{-- Baris untuk Istri/Suami akan muncul di sini --}}
                                    <div id="pasangan-container"></div>
                                    {{-- Baris untuk Anak-anak akan muncul di sini --}}
                                    <div id="anak-container"></div>
                                </div>

                                {{-- Template untuk baris input (disembunyikan) --}}
                                <div id="keluarga-template" style="display: none;">
                                    <div class="row align-items-center keluarga-row mb-2">
                                        <div class="col-md-3">
                                            <label>Hubungan</label>
                                            <input type="text" name="keluarga[0][hubungan]" class="form-control"
                                                readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Nama</label>
                                            <input type="text" name="keluarga[0][nama]" class="form-control"
                                                placeholder="Nama">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Tempat Lahir</label>
                                            <input type="text" name="keluarga[0][tempat_lahir]"
                                                class="form-control" placeholder="Tempat Lahir">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Tanggal Lahir</label>
                                            <input type="date" name="keluarga[0][tanggal_lahir]"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                {{-- AKHIR BLOK DATA KELUARGA --}}
                                <div class="form-group">
                                    <label for="">Alamat KTP</label>
                                    <br>
                                    <textarea name="alamat" id="alamat" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="">Domisili</label>
                                    <br>
                                    <textarea name="domisili" id="domisili" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group mt-2">
                                    <label for="">No. Rekening <span class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="no_rekening" id="no_rekening" class="form-control"
                                        required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Atas Nama Rekening</label>
                                    <br>
                                    <input type="text" name="nama_rekening" id="nama_rekening"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Nama Bank</label>
                                    <br>
                                    <select name="nama_bank" id="nama_bank" class="form-control"
                                        style="width: 100%;">
                                        <option value="">Pilih Bank Rekening</option>
                                        <option value="MANDIRI">MANDIRI</option>
                                        <option value="BRI">BRI</option>
                                        <option value="BNI">BNI</option>
                                        <option value="BCA">BCA</option>
                                        <option value="BSI">BSI</option>
                                    </select>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Nama Ibu Kandung</label>
                                    <br>
                                    <input type="text" name="nama_ibu_kandung" id="nama_ibu_kandung"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Jenjang Pendidikan Terakhir</label>
                                    <br>
                                    <select name="jenjang_pendidikan" id="jenjang_pendidikan" class="form-control"
                                        style="width: 100%;">
                                        <option value="">Pilih Jenjang Pendidikan Terakhir</option>
                                        <option value="SD">SD</option>
                                        <option value="SMP">SMP</option>
                                        <option value="SMA">SMA</option>
                                        <option value="D1">D1</option>
                                        <option value="D2">D2</option>
                                        <option value="D3">D3</option>
                                        <option value="D4">D4</option>
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Jurusan Pendidikan Terakhir</label>
                                    <br>
                                    <input type="text" name="jurusan_pendidikan" id="jurusan_pendidikan"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">No. Telepon <span class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="no_telp" id="no_telp" class="form-control"
                                        required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No.Telp Darurat</label>
                                    <br>
                                    <input type="text" name="no_telp_darurat" id="no_telp_darurat"
                                        class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Email <span class="text-danger">*</span></label>
                                    <br>
                                    <small>Note : Email ini untuk keperluan notifikasi sistem</small>
                                    <br>
                                    <input type="email" name="email" id="email" class="form-control"
                                        required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">NPWP</label>
                                    <br>
                                    <input type="text" name="npwp" id="npwp" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. BPJS Ketenagakerjaan</label>
                                    <br>
                                    <input type="text" name="no_bpjs_kt" id="no_bpjs_kt" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. BPJS Kesehatan</label>
                                    <br>
                                    <input type="text" name="no_bpjs_ks" id="no_bpjs_ks" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Posisi <span class="text-danger">*</span></label>
                                    <select class="form-control" multiple="multiple" data-placeholder="Pilih Posisi"
                                        id="posisi" name="posisi[]" style="width: 100%;" required>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">PIN Fingerprint</label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="pin" id="pin" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tipe_karyawan">Tipe Karyawan <span
                                            class="text-danger">*</span></label>
                                    <select name="tipe_karyawan" id="tipe_karyawan" class="form-control select2"
                                        data-placeholder="Pilih Tipe Karyawan" style="width: 100%;" required>
                                        <option value="">Pilih Tipe Karyawan</option>
                                        <option value="D">DIRECT</option>
                                        <option value="I">INDIRECT</option>
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <input type="checkbox" id="isAdmin" name="isAdmin" value="Y"
                                        class="filled-in chk-col-primary" />
                                    <label for="isAdmin">
                                        <h5>Is Admin</h5>
                                    </label>
                                </div>
                                <small>Note : Check tombol ini jika user merupakan seorang admin departemen,
                                    memiliki
                                    beberapa hak akses modul
                                    tertentu.</small>
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
