<!-- modal Area -->
<div class="modal fade" id="modal-input-template">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Template</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('master-data.template.store') }}" method="POST"
                        enctype="multipart/form-data" id="form-tambah-template">
                        @csrf
                        <div class="form-group">
                            <label for="">Type</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <select name="type_template" id="type_template" class="form-control" required>
                                    <option value="PKWT">PKWT</option>
                                    <option value="PKWTT">PKWTT</option>
                                    <option value="MAGANG">MAGANG</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Nama Template</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="text" name="nama_template" id="nama_template" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">File Template</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="file" name="file_template" id="file_template" class="form-control">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-2">
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
