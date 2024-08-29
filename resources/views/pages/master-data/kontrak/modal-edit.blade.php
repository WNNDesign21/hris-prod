<!-- modal Area -->
<div class="modal fade" id="modal-edit-kontrak">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title p-4" id="modal-edit-kontrak-title">Edit Kontrak</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-edit-kontrak">
                        @csrf
                        @method('PATCH')
                        <div class="row p-4">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="">ID Kontrak</label>
                                    <input type="text" name="id_kontrakEdit" id="id_kontrakEdit" class="form-control"
                                        disabled>
                                </div>
                                <div class="form-group">
                                    <label for="">Nama Karyawan</label>
                                    <input type="text" name="nama_karyawan_kontrakEdit"
                                        id="nama_karyawan_kontrakEdit" class="form-control" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="">No. Surat</label>
                                    <input type="text" name="no_surat_kontrakEdit" id="no_surat_kontrakEdit"
                                        class="form-control" placeholder="Contoh : 001">
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Dibuat</label>
                                    <input type="date" name="issued_date_kontrakEdit" id="issued_date_kontrakEdit"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Tempat</label>
                                    <select name="tempat_administrasi_kontrakEdit" id="tempat_administrasi_kontrakEdit"
                                        class="form-control" style="width: 100%;">
                                        <option value="Karawang">Karawang</option>
                                        <option value="Sadang">Sadang</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Jenis Kontrak <span class="text-danger">*</span></label>
                                    <select name="jenis_kontrakEdit" id="jenis_kontrakEdit" class="form-control"
                                        style="width: 100%;">
                                        <option value="PKWT">PKWT</option>
                                        <option value="PKWTT">PKWTT</option>
                                        <option value="MAGANG">MAGANG</option>
                                        <option value="THL">THL</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label class="form-label">Posisi <span class="text-danger">*</span></label>
                                    <select class="form-control" id="posisi_kontrakEdit" name="posisi_kontrakEdit"
                                        style="width: 100%;">
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Durasi (Dalam Bulan)</label>
                                    <input type="number" name="durasi_kontrakEdit" id="durasi_kontrakEdit"
                                        class="form-control" placeholder="Note : Abaikan jika memilih PKWTT">
                                </div>
                                <div class="form-group">
                                    <label for="">Salary</label>
                                    <input type="text" name="salary_kontrakEdit" id="salary_kontrakEdit"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai_kontrakEdit"
                                        id="tanggal_mulai_kontrakEdit" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai_kontrakEdit"
                                        id="tanggal_selesai_kontrakEdit" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Deskripsi</label>
                                    <textarea name="deskripsi_kontrakEdit" id="deskripsi_kontrakEdit" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                    Update</button>
                            </div>
                        </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</div>
<!-- /.modal -->
