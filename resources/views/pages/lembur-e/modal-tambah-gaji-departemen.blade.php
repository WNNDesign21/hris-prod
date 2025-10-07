<!-- modal Area -->
<div class="modal fade" id="modal-tambah-gaji-departemen">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Gaji Departemen</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('lembure.setting-gaji-departemen.store') }}" method="POST"
                        enctype="multipart/form-data" id="form-tambah-gaji-departemen">
                        @csrf
                        <label for="">Periode</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="month" name="periode" id="periode" class="form-control" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                Tambah</button>
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
