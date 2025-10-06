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
                                    <label for="">Organisasi <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="hidden" name="id_organisasiEdit" id="id_organisasiEdit">
                                        <select name="organisasiEdit" id="organisasiEdit" class="form-control"
                                            style="width: 100%;" required>
                                            <option value="">Pilih Organisasi</option>
                                            @foreach ($organisasi as $org)
                                                <option value="{{ $org->id_organisasi }}">{{ $org->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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
                                            <label for="">Kota Lahir</label>
                                            <br>
                                            <div class="input-group mb-2" style="width:100%;">
                                                <input type="text" name="tempat_lahirEdit" id="tempat_lahirEdit"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Tanggal Lahir</label>
                                            <br>
                                            <div class="input-group mb-2" style="width:100%;">
                                                <input type="date" name="tanggal_lahirEdit" id="tanggal_lahirEdit"
                                                    class="form-control">
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
                                    <label for="">Agama</label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <select name="agamaEdit" id="agamaEdit" class="form-control"
                                            style="width: 100%;">
                                            <option value="">Pilih Agama (Boleh Kosong)</option>
                                            <option value="ISLAM">ISLAM</option>
                                            <option value="KRISTEN">KRISTEN</option>
                                            <option value="KATOLIK">KATHOLIK</option>
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
                                    <label for="kategori_keluargaEdit">Kategori Keluarga</label>
                                    <select class="form-control" id="kategori_keluargaEdit"
                                        name="kategori_keluargaEdit">
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
                                    <label for="status_kawinEdit">Status Kawin <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" id="status_kawinEdit" name="status_kawinEdit"
                                        required>
                                        <option value="">Pilih Status</option>
                                        <option value="BK">Belum Kawin (BK)</option>
                                        <option value="K">Kawin (K)</option>
                                        <option value="KA1">Kawin Anak 1 (KA1)</option>
                                        <option value="KA2">Kawin Anak 2 (KA2)</option>
                                        <option value="KA3">Kawin Anak 3 (KA3)</option>
                                        {{-- Tambahkan opsi lain jika perlu, misal: KA4, KA5 --}}
                                    </select>
                                </div>

                                <hr>
                                <h5>Data Anggota Keluarga</h5>
                                <div id="keluarga-wrapperEdit">
                                    {{-- Baris untuk Istri/Suami akan muncul di sini --}}
                                    <div id="pasangan-containerEdit"></div>
                                    {{-- Baris untuk Anak-anak akan muncul di sini --}}
                                    <div id="anak-containerEdit"></div>
                                </div>

                                {{-- Template untuk baris input (disembunyikan) --}}
                                <div id="keluarga-templateEdit" style="display: none;">
                                    <div class="row align-items-center keluarga-row mb-2">
                                        <input type="hidden" name="keluargaEdit[0][id]">
                                        <div class="col-md-3">
                                            <label>Hubungan</label>
                                            <input type="text" name="keluargaEdit[0][hubungan]"
                                                class="form-control" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Nama</label>
                                            <input type="text" name="keluargaEdit[0][nama]" class="form-control"
                                                placeholder="Nama">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Tempat Lahir</label>
                                            <input type="text" name="keluargaEdit[0][tempat_lahir]"
                                                class="form-control" placeholder="Tempat Lahir">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Tanggal Lahir</label>
                                            <input type="date" name="keluargaEdit[0][tanggal_lahir]"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                {{-- AKHIR BLOK DATA KELUARGA --}}
                                <div class="form-group">
                                    <label for="">Alamat KTP</label>
                                    <br>
                                    <textarea name="alamatEdit" id="alamatEdit" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="">Domisili</label>
                                    <br>
                                    <textarea name="domisiliEdit" id="domisiliEdit" class="form-control"></textarea>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. Rekening <span class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="no_rekeningEdit" id="no_rekeningEdit"
                                        class="form-control" required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Atas Nama Rekening</label>
                                    <br>
                                    <input type="text" name="nama_rekeningEdit" id="nama_rekeningEdit"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Nama Bank</label>
                                    <br>
                                    <select name="nama_bankEdit" id="nama_bankEdit" class="form-control"
                                        style="width: 100%;">
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
                                    <label for="">Nama Ibu Kandung</label>
                                    <br>
                                    <input type="text" name="nama_ibu_kandungEdit" id="nama_ibu_kandungEdit"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Jenjang Pendidikan Terakhir</label>
                                    <br>
                                    <select name="jenjang_pendidikanEdit" id="jenjang_pendidikanEdit"
                                        class="form-control" style="width: 100%;">
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
                                    <input type="text" name="jurusan_pendidikanEdit" id="jurusan_pendidikanEdit"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">No. Telepon <span class="text-danger">*</span></label>
                                    <br>
                                    <input type="text" name="no_telpEdit" id="no_telpEdit" class="form-control"
                                        required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No.Telp Darurat</label>
                                    <br>
                                    <input type="text" name="no_telp_daruratEdit" id="no_telp_daruratEdit"
                                        class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Email</label>
                                    <br>
                                    <small>Note : Email ini untuk keperluan notifikasi sistem</small>
                                    <br>
                                    <input type="email" name="emailEdit" id="emailEdit" class="form-control"
                                        required>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">NPWP</label>
                                    <br>
                                    <input type="text" name="npwpEdit" id="npwpEdit" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. BPJS Ketenagakerjaan</label>
                                    <br>
                                    <input type="text" name="no_bpjs_ktEdit" id="no_bpjs_ktEdit"
                                        class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. BPJS Kesehatan</label>
                                    <br>
                                    <input type="text" name="no_bpjs_ksEdit" id="no_bpjs_ksEdit"
                                        class="form-control">
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
                                    <label for="">PIN Fingerprint</label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="pinEdit" id="pinEdit" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tipe_karyawanEdit">Tipe Karyawan <span
                                            class="text-danger">*</span></label>
                                    <select name="tipe_karyawanEdit" id="tipe_karyawanEdit"
                                        class="form-control select2" data-placeholder="Pilih Tipe Karyawan"
                                        style="width: 100%;" required>
                                        <option value="">Pilih Tipe Karyawan</option>
                                        <option value="D">DIRECT</option>
                                        <option value="I">INDIRECT</option>
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <input type="checkbox" id="isAdminEdit" name="isAdminEdit" value="Y"
                                        class="filled-in chk-col-primary" />
                                    <label for="isAdminEdit">
                                        <h5>Is Admin</h5>
                                    </label>
                                </div>
                                <small>Note : Check tombol ini jika user merupakan seorang admin departemen, memiliki
                                    beberapa hak akses modul
                                    tertentu.</small>
                                <div class="row mt-3">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Jatah Cuti Pribadi <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="sisa_cuti_pribadiEdit"
                                                id="sisa_cuti_pribadiEdit" class="form-control" min='0'
                                                max='12' required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Jatah Cuti Bersama <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="sisa_cuti_bersamaEdit"
                                                id="sisa_cuti_bersamaEdit" class="form-control" min='0'
                                                max='12' required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Jatah Cuti Tahun Lalu <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="sisa_cuti_tahun_laluEdit"
                                                id="sisa_cuti_tahun_laluEdit" class="form-control" min='0'
                                                max='12' required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Expired Date Cuti Tahun Lalu <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="expired_date_cuti_tahun_laluEdit"
                                                id="expired_date_cuti_tahun_laluEdit" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Hutang Cuti <span
                                                    class="text-danger">*</span></label>
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
