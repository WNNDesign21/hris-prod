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
                                    <label for="">Nomor Induk Karyawan <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="ni_karyawan" id="ni_karyawan" class="form-control"
                                            required>
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
                                            <label for="">Kota Lahir <span
                                                    class="text-danger">*</span></label>
                                            <br>
                                            <div class="input-group mb-2" style="width:100%;">
                                                <input type="text" name="tempat_lahir" id="tempat_lahir"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Tanggal Lahir <span
                                                    class="text-danger">*</span></label>
                                            <br>
                                            <div class="input-group mb-2" style="width:100%;">
                                                <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                                    class="form-control" required>
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
                                    <label for="">Agama <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <select name="agama" id="agama" class="form-control"
                                            style="width: 100%;" required>
                                            <option value="">Pilih Agama (Boleh Kosong)</option>
                                            <option value="ISLAM">ISLAM</option>
                                            <option value="KRISTEN">KRISTEN</option>
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
                                    <label for="">Kategori Keluarga <span class="text-danger">*</span></label>
                                    <br>
                                    <select name="kategori_keluarga" id="kategori_keluarga" class="form-control"
                                        style="width: 100%;" required>
                                        <option value="">Pilih Kategori Keluarga</option>
                                        <option value="TK0">TK0</option>
                                        <option value="TK1">TK1</option>
                                        <option value="TK2">TK2</option>
                                        <option value="TK3">TK3</option>
                                        <option value="K0">K0</option>
                                        <option value="K1">K1</option>
                                        <option value="K2">K2</option>
                                        <option value="K3">K3</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Alamat KTP <span class="text-danger">*</span></label>
                                    <br>
                                    <textarea name="alamat" id="alamat" class="form-control" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="">Domisili <span class="text-danger">*</span></label>
                                    <br>
                                    <textarea name="domisili" id="domisili" class="form-control" required></textarea>
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
                                    <label for="">Atas Nama Rekening <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="nama_rekening" id="nama_rekening"
                                        class="form-control" required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Nama Ibu Kandung <span class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="nama_ibu_kandung" id="nama_ibu_kandung"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Jenjang Pendidikan Terakhir <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <select name="jenjang_pendidikan" id="jenjang_pendidikan" class="form-control"
                                        style="width: 100%;" required>
                                        <option value="">Pilih Jenjang Pendidikan Terakhir</option>
                                        <option value="SD">SD</option>
                                        <option value="SMP">SMP</option>
                                        <option value="SMA">SMA</option>
                                        <option value="D1">D1</option>
                                        <option value="D2">D2</option>
                                        <option value="D3">D3</option>
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Jurusan Pendidikan Terakhir <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="jurusan_pendidikan" id="jurusan_pendidikan"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="">No. Telepon <span class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="no_telp" id="no_telp" class="form-control"
                                        required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No.Telp Darurat <span class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="no_telp_darurat" id="no_telp_darurat"
                                        class="form-control" required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Email <span class="text-danger">*</span></label>
                                    <br>
                                    <small>Note : Email ini untuk keperluan notifikasi sistem</small>
                                    <br>
                                    <input type="email" name="email" id="email" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">NPWP <span class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="npwp" id="npwp" class="form-control"
                                        required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. BPJS Kesehatan <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="no_bpjs_ks" id="no_bpjs_ks" class="form-control"
                                        required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. BPJS Ketenagakerjaan <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="no_bpjs_kt" id="no_bpjs_kt" class="form-control"
                                        required>
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
                                {{-- <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Jatah Cuti <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="sisa_cuti" id="sisa_cuti"
                                                class="form-control" min='0' max='12' required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Hutang Cuti <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="hutang_cuti" id="hutang_cuti"
                                                class="form-control" min='0' max='12' required>
                                        </div>
                                    </div>
                                </div> --}}
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
