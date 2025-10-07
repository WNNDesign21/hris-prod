<!-- modal Area -->
<div class="modal fade" id="modal-input-grup">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Grup</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('master-data.grup.store') }}" method="POST" enctype="multipart/form-data"
                    id="form-tambah-grup">
                    @csrf
                    <div class="form-group">
                        <label for="">Nama</label>
                        <input type="text" name="nama_grup" id="nama_grup" class="form-control" style="width: 100%;"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="">Jam Masuk</label>
                        <input type="time" name="jam_masuk" id="jam_masuk" class="form-control" style="width: 100%;"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="">Jam Keluar</label>
                        <input type="time" name="jam_keluar" id="jam_keluar" class="form-control"
                            style="width: 100%;" required>
                    </div>
                    <div class="form-group">
                        <label for="">Toleransi Waktu (Menit)</label>
                        <input type="number" name="toleransi_waktu" id="toleransi_waktu" class="form-control"
                            style="width: 100%;" min="0" required>
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                            Tambah</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
