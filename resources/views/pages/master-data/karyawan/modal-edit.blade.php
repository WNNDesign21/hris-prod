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
                                    <label for="">No. KTP</label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="no_ktpEdit" id="no_ktpEdit" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">NIK</label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="nikEdit" id="nikEdit" class="form-control">
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
                                        <select name="gol_darahEdit" id="gol_darahEdit" class="form-control"
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
                                    <select name="status_keluargaEdit" id="status_keluargaEdit" class="form-control"
                                        style="width: 100%;">
                                        <option value="">Pilih Status Keluarga</option>
                                        <option value="MENIKAH">Menikah</option>
                                        <option value="LAJANG">Lajang</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Alamat</label>
                                    <br>
                                    <textarea name="alamatEdit" id="alamatEdit" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="">No. Telepon</label>
                                    <br>
                                    <input type="text" name="no_telpEdit" id="no_telpEdit" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Email</label>
                                    <br>
                                    <small>Note : Email ini untuk keperluan notifikasi sistem</small>
                                    <br>
                                    <input type="email" name="emailEdit" id="emailEdit" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">NPWP</label>
                                    <br>
                                    <input type="text" name="npwpEdit" id="npwpEdit" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. BPJS Kesehatan</label>
                                    <br>
                                    <input type="text" name="no_bpjs_ksEdit" id="no_bpjs_ksEdit"
                                        class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">No. BPJS Ketenagakerjaan</label>
                                    <br>
                                    <input type="text" name="no_bpjs_ktEdit" id="no_bpjs_ktEdit"
                                        class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="">Tahun Masuk <span class="text-danger">*</span></label>
                                    <br>
                                    <select name="tahun_masukEdit" id="tahun_masukEdit" class="form-control"
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
                                    <select name="status_karyawanEdit" id="status_karyawanEdit" class="form-control"
                                        style="width: 100%;">
                                        <option value="AKTIF">AKTIF</option>
                                        <option value="RESIGN">RESIGN</option>
                                        <option value="TERMINASI">TERMINASI</option>
                                        <option value="PENSIUN">PENSIUN</option>
                                    </select>
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
                                <div class="form-group">
                                    <label class="form-label">Jatah Cuti <span class="text-danger">*</span></label>
                                    <input type="number" name="sisa_cutiEdit" id="sisa_cutiEdit"
                                        class="form-control" min='0' max='12' required>
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
