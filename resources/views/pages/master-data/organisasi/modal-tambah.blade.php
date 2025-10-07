<!-- modal Area -->
<div class="modal fade" id="modal-input-org">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Organisasi</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('master-data.organisasi.store') }}" method="POST"
                        enctype="multipart/form-data" id="form-tambah-org">
                        @csrf
                        <label for="">Nama</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="nama_org" id="nama_org" class="form-control" required>
                        </div>
                        <label for="">Alamat</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <textarea type="text" name="alamat_org" id="alamat_org" class="form-control"></textarea>
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
