<!-- modal Area -->
<div class="modal fade" id="modal-edit-karyawan">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title p-4">Detail Karyawan</h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-edit-karyawan">
                        @method('PATCH')
                        @csrf
                        <div class="row p-4">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <a id="link_fotoEdit" href="{{ asset('img/no-image.png') }}"
                                        data-title="Foto Karyawan" class="image-popup-vertical-fit">
                                        <img id="image_reviewEdit" src="{{ asset('img/no-image.png') }}"
                                            alt="Foto Karyawan" style="width: 150px;height: 150px;"
                                            class="img-thumbnail img-fluid">
                                    </a>
                                </div>
                                <div class="form-group">
                                    <label for="">Foto</label>
                                    <input type="file" name="fotoEdit" id="fotoEdit" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Nomor Induk Karyawan <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="ni_karyawanEdit" id="ni_karyawanEdit"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Nama <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="hidden" name="id_karyawanEdit" id="id_karyawanEdit"
                                            class="form-control" required>
                                        <input type="text" name="namaEdit" id="namaEdit" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">No. KK <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="no_kkEdit" id="no_kkEdit" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">NIK <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="nikEdit" id="nikEdit" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Kota Lahir <span class="text-danger">*</span></label>
                                            <br>
                                            <div class="input-group mb-2" style="width:100%;">
                                                <input type="text" name="tempat_lahirEdit" id="tempat_lahirEdit"
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
                                                <input type="date" name="tanggal_lahirEdit" id="tanggal_lahirEdit"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <select name="jenis_kelaminEdit" id="jenis_kelaminEdit" class="form-control"
                                            style="width: 100%;" required>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Agama <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <select name="agamaEdit" id="agamaEdit" class="form-control"
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
                                        <select name="gol_darahEdit" id="gol_darahEdit" class="form-control"
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
                                    <select name="status_keluargaEdit" id="status_keluargaEdit" class="form-control"
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
                                    <select name="kategori_keluargaEdit" id="kategori_keluargaEdit"
                                        class="form-control" style="width: 100%;" required>
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
                                    <textarea name="alamatEdit" id="alamatEdit" class="form-control" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="">Domisili <span class="text-danger">*</span></label>
                                    <br>
                                    <textarea name="domisiliEdit" id="domisiliEdit" class="form-control" required></textarea>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. Rekening <span class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="no_rekeningEdit" id="no_rekeningEdit"
                                        class="form-control" required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Atas Nama Rekening <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="nama_rekeningEdit" id="nama_rekeningEdit"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Nama Bank <span class="text-danger">*</span></label>
                                    <br>
                                    <select name="nama_bankEdit" id="nama_bankEdit" class="form-control"
                                        style="width: 100%;" required>
                                        <option value="">Pilih Bank Rekening</option>
                                        <option value="MANDIRI">MANDIRI</option>
                                        <option value="BRI">BRI</option>
                                        <option value="BNI">BNI</option>
                                        <option value="BCA">BCA</option>
                                        <option value="BSI">BSI</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group mt-2">
                                    <label for="">Nama Ibu Kandung <span class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="nama_ibu_kandungEdit" id="nama_ibu_kandungEdit"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Jenjang Pendidikan Terakhir <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <select name="jenjang_pendidikanEdit" id="jenjang_pendidikanEdit"
                                        class="form-control" style="width: 100%;" required>
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
                                    <input type="text" name="jurusan_pendidikanEdit" id="jurusan_pendidikanEdit"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="">No. Telepon <span class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="no_telpEdit" id="no_telpEdit" class="form-control"
                                        required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No.Telp Darurat <span class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="no_telp_daruratEdit" id="no_telp_daruratEdit"
                                        class="form-control" required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Email</label>
                                    <br>
                                    <small>Note : Email ini untuk keperluan notifikasi sistem</small>
                                    <br>
                                    <input type="email" name="emailEdit" id="emailEdit" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">NPWP <span class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="npwpEdit" id="npwpEdit" class="form-control"
                                        required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. BPJS Kesehatan <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="no_bpjs_ksEdit" id="no_bpjs_ksEdit"
                                        class="form-control" required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. BPJS Ketenagakerjaan <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="no_bpjs_ktEdit" id="no_bpjs_ktEdit"
                                        class="form-control" required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal_mulaiEdit"
                                        id="tanggal_mulaiEdit" readonly>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal_selesaiEdit"
                                        id="tanggal_selesaiEdit" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Status Karyawan <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="status_karyawanEdit"
                                        id="status_karyawanEdit" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Posisi <span class="text-danger">*</span></label>
                                    <select class="form-control" multiple="multiple" data-placeholder="Pilih Posisi"
                                        id="posisiEdit" name="posisiEdit[]" style="width: 100%;" required>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Grup <span class="text-danger">*</span></label>
                                    <select class="form-control" id="grupEdit" name="grupEdit" style="width: 100%;"
                                        required>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Jatah Cuti Pribadi</label>
                                            <input type="number" name="sisa_cuti_pribadiEdit"
                                                id="sisa_cuti_pribadiEdit" class="form-control" min='0'
                                                max='12' required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Jatah Cuti Bersama</label>
                                            <input type="number" name="sisa_cuti_bersamaEdit"
                                                id="sisa_cuti_bersamaEdit" class="form-control" min='0'
                                                max='12' required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Jatah Cuti Tahun Lalu</label>
                                            <input type="number" name="sisa_cuti_tahun_laluEdit"
                                                id="sisa_cuti_tahun_laluEdit" class="form-control" min='0'
                                                max='12' required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Expired Date Cuti Tahun Lalu</label>
                                            <input type="date" name="expired_date_cuti_tahun_laluEdit"
                                                id="expired_date_cuti_tahun_laluEdit" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Hutang Cuti</label>
                                            <input type="number" name="hutang_cutiEdit" id="hutang_cutiEdit"
                                                class="form-control" min='0' max='12' required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                    Update</button>
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
