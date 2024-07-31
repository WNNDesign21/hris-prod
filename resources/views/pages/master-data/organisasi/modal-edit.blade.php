<!-- modal Area -->
<div class="modal fade" id="modal-edit-org">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Organisasi</h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-edit-org">
                        @method('PATCH')
                        @csrf
                        <input type="hidden" name="id_org_edit" id="id_org_edit">
                        <label for="">Nama</label>
                        <div class="input-group" style="width:100%;">
                            <input type="text" name="nama_org_edit" id="nama_org_edit" class="form-control">
                        </div>
                        <label for="" class="mt-2">Alamat</label>
                        <div class="input-group" style="width:100%;">
                            <textarea type="text" name="alamat_org_edit" id="alamat_org_edit" class="form-control"></textarea>
                        </div>
                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                Update</button>
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
