<!-- modal Area -->
<div class="modal fade" id="modal-input-izin-pribadi">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Izin Pribadi</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-input-izin-pribadi">
                        @csrf
                        <div class="row p-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Jenis Izin<span class="text-danger">*</span></label>
                                    <select name="jenis_izin" id="jenis_izin" class="form-control" style="width: 100%;">
                                        <option value="SK">SAKIT</option>
                                        <option value="TM">IZIN TIDAK MASUK</option>
                                        <option value="SH">IZIN 1/2 HARI</option>
                                    </select>
                                </div>
                                <div class="form-group" id="conditional_field">
                                    <div class="form-group">
                                        <label for="lampiran">Lampiran</label>
                                        <input type="file" class="form-control" id="lampiran" name="lampiran">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <textarea class="form-control" name="keterangan" id="keterangan" style="width: 100%;"></textarea>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                        Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
