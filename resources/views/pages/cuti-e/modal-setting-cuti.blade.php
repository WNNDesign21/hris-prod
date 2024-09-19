<!-- modal Area -->
<div class="modal fade" id="modal-setting-cuti">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Cuti Khusus</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('cutie.setting-cuti.store') }}" method="POST" enctype="multipart/form-data"
                        id="form-setting-cuti">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <input type="hidden" id="id_jenis_cuti" name="id_jenis_cuti">
                                <div class="form-group">
                                    <label for="jenis">Jenis Cuti</label>
                                    <input type="text" class="form-control" id="jenis" name="jenis">
                                </div>
                                <div class="form-group">
                                    <label for="jenis">Durasi</label>
                                    <input type="number" class="form-control" id="durasi" name="durasi"
                                        min="1" required>
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
