<!-- modal Area -->
<div class="modal fade" id="modal-input-grup">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Grup</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('master-data.grup.store') }}" method="POST" enctype="multipart/form-data"
                        id="form-tambah-grup">
                        @csrf
                        <label for="">Nama</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="nama_grup" id="nama_grup" class="form-control" required>
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
