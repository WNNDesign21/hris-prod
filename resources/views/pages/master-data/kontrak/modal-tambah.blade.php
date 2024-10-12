<!-- modal Area -->
<div class="modal fade" id="modal-input-kontrak">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title p-4">Tambah Kontrak</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('master-data.kontrak.store') }}" method="POST" enctype="multipart/form-data"
                        id="form-input-kontrak">
                        @csrf
                        <div class="row p-4">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="">Karyawan <span class="text-danger">*</span></label>
                                    <select class="form-control" multiple="multiple"
                                        data-placeholder="Pilih Multi Karyawan" id="karyawan_id" name="karyawan_id[]"
                                        style="width: 100%;" required>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">No. Surat</label>
                                    <input type="text" name="no_surat" id="no_surat" class="form-control"
                                        placeholder="Contoh : 001 (Otomatis Berurutan Jika Memilih Multi Karyawan)">
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Dibuat</label>
                                    <input type="date" name="issued_date" id="issued_date" class="form-control"
                                        required value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="form-group">
                                    <label for="">Tempat</label>
                                    <select name="tempat_administrasi" id="tempat_administrasi" class="form-control"
                                        style="width: 100%;">
                                        <option value="Karawang">Karawang</option>
                                        <option value="Purwakarta">Purwakarta</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Jenis Perjanjian Kerja <span
                                            class="text-danger">*</span></label>
                                    <select name="jenis" id="jenis" class="form-control select2"
                                        style="width: 100%;">
                                        <option value="PKWT">PKWT</option>
                                        <option value="PKWTT">PKWTT</option>
                                        <option value="MAGANG">MAGANG</option>
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <input type="checkbox" id="isReactive" name="isReactive" value="Y"
                                        class="filled-in chk-col-primary" />
                                    <label for="isReactive">
                                        <h5>IsReactive</h5>
                                    </label>
                                </div>
                                <small>Note : Khusus untuk karyawan yang sudah keluar namun masuk
                                    kembali.</small>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label class="form-label">Posisi <span class="text-danger">*</span></label>
                                    <select class="form-control" id="posisi" name="posisi" style="width: 100%;"
                                        required>
                                    </select>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label for="">Nama Posisi</label>
                                    <br>
                                    <small>Note : Jika mengisi ini, maka nama posisi ini yang akan muncul di Template
                                        Kontrak, namun jika kosong, maka akan mengikuti bawaan dari Master Data
                                        Posisi</small>
                                    <input type="text" name="nama_posisi" id="nama_posisi" class="form-control">
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label for="">Durasi (Dalam Bulan)</label>
                                    <input type="number" name="durasi" id="durasi" class="form-control"
                                        placeholder="Note : Abaikan jika memilih PKWTT">
                                </div>
                                <div class="form-group">
                                    <label for="">Salary</label>
                                    <input type="text" name="salary" id="salary" class="form-control"
                                        placeholder="Tidak menggunakan tanda baca">
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Deskripsi</label>
                                    <textarea name="deskripsi" id="deskripsi" class="form-control"></textarea>
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
