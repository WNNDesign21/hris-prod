<!-- modal Area -->
<div class="modal fade" id="modal-edit-template">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Template</h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-edit-template">
                        @method('PATCH')
                        @csrf
                        <input type="hidden" name="id_template_edit" id="id_template_edit">
                        <div class="form-group">
                            <label for="">Type</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <select name="type_template_edit" id="type_template_edit" class="form-control" required>
                                    <option value="PKWT">PKWT</option>
                                    <option value="PKWTT">PKWTT</option>
                                    <option value="MAGANG">MAGANG</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Nama Template</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="text" name="nama_template_edit" id="nama_template_edit"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">isActive</label>
                            <br>
                            <small>Note : Template akan digunakan ketika download template ketika aktif.</small>
                            <div class="input-group mb-2" style="width:100%;">
                                <select name="isactive_template_edit" id="isactive_template_edit" class="form-control"
                                    required>
                                    <option value="Y">AKTIF</option>
                                    <option value="N">NON-AKTIF</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">File Template</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="file" name="file_template_edit" id="file_template_edit"
                                    class="form-control">
                            </div>
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
