<!-- modal Area -->
<div class="modal fade" id="modal-edit-grup">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Grup</h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data" id="form-edit-grup">
                    @method('PATCH')
                    @csrf
                    <input type="hidden" name="id_grup_edit" id="id_grup_edit">
                    <div class="form-group">
                        <label for="">Nama</label>
                        <input type="text" name="nama_grup_edit" id="nama_grup_edit" class="form-control"
                            style="width: 100%;" required>
                    </div>
                    <div class="form-group">
                        <label for="">Jam Masuk</label>
                        <input type="time" name="jam_masuk_edit" id="jam_masuk_edit" class="form-control"
                            style="width: 100%;" required>
                    </div>
                    <div class="form-group">
                        <label for="">Jam Keluar</label>
                        <input type="time" name="jam_keluar_edit" id="jam_keluar_edit" class="form-control"
                            style="width: 100%;" required>
                    </div>
                    <div class="form-group">
                        <label for="">Toleransi Waktu (Menit)</label>
                        <input type="number" name="toleransi_waktu_edit" id="toleransi_waktu_edit" class="form-control"
                            style="width: 100%;" min="0" required>
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                            Update</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
