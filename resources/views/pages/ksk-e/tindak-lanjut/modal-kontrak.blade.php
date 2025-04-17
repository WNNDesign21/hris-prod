<!-- modal Area -->
<div class="modal fade" id="modal-kontrak">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title p-4">Tambah Kontrak</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('ksk.tindak-lanjut.store-kontrak') }}" method="POST"
                        enctype="multipart/form-data" id="form-kontrak">
                        @csrf
                        <div class="row p-4">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="">Karyawan <span class="text-danger">*</span></label>
                                    <input type="hidden" name="id_detail_kskKontrak" id="id_detail_kskKontrak">
                                    <select class="form-control" data-placeholder="Pilih Karyawan"
                                        id="karyawan_idKontrak" name="karyawan_idKontrak" style="width: 100%;" required>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">No. Surat</label>
                                    <input type="text" name="no_suratKontrak" id="no_suratKontrak"
                                        class="form-control"
                                        placeholder="Contoh : 001 (Otomatis Berurutan Jika Memilih Multi Karyawan)"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Dibuat</label>
                                    <input type="date" name="issued_dateKontrak" id="issued_dateKontrak"
                                        class="form-control" required value="{{ date('Y-m-d') }}" readonly required>
                                </div>
                                <div class="form-group">
                                    <label for="">Tempat</label>
                                    <input type="text" name="tempat_administrasiKontrak"
                                        id="tempat_administrasiKontrak" class="form-control" readonly required>
                                </div>
                                <div class="form-group">
                                    <label for="">Jenis Perjanjian Kerja <span
                                            class="text-danger">*</span></label>
                                    <select name="jenisKontrak" id="jenisKontrak" class="form-control select2"
                                        style="width: 100%;" required>
                                        <option value="PKWT">PKWT</option>
                                        <option value="PKWTT">PKWTT</option>
                                        <option value="MAGANG">MAGANG</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label class="form-label">Posisi <span class="text-danger">*</span></label>
                                    <select class="form-control" id="posisiKontrak" name="posisiKontrak"
                                        style="width: 100%;" required>
                                    </select>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label for="">Nama Posisi</label>
                                    <br>
                                    <small>Note : Jika mengisi ini, maka nama posisi ini yang akan muncul di Template
                                        Kontrak, namun jika kosong, maka akan mengikuti bawaan dari Master Data
                                        Posisi</small>
                                    <input type="text" name="nama_posisiKontrak" id="nama_posisiKontrak"
                                        class="form-control">
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label for="">Durasi (Dalam Bulan)</label>
                                    <input type="number" name="durasiKontrak" id="durasiKontrak" class="form-control"
                                        placeholder="Note : Abaikan jika memilih PKWTT" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="">Salary</label>
                                    <input type="text" name="salaryKontrak" id="salaryKontrak" class="form-control"
                                        placeholder="Tidak menggunakan tanda baca" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulaiKontrak" id="tanggal_mulaiKontrak"
                                        class="form-control" readonly required>
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesaiKontrak" id="tanggal_selesaiKontrak"
                                        class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="">Deskripsi</label>
                                    <textarea name="deskripsiKontrak" id="deskripsiKontrak" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                    Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</div>
<!-- /.modal -->
