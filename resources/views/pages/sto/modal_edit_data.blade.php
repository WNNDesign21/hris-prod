<!-- modal Area -->
<div class="modal fade" id="modal-edit-sto">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Data STO</h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-edit-sto">
                        @method('PATCH')
                        @csrf
                        <input type="hidden" name="id_sto_edit" id="id_sto_edit">
                        <label for="">No Label</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="no_label_edit" id="no_label_edit" class="form-control">
                        </div>
                        <label for="">Customer</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <textarea type="text" name="customer_edit" id="customer_edit" class="form-control"></textarea>
                        </div>
                        <label for="">Quantity</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <textarea type="text" name="quantity" id="quantity" class="form-control"></textarea>
                        </div>
                        <label for="">Identitas (LOT)</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <textarea type="text" name="identitas_lot_edit" id="identitas_lot_edit" class="form-control"></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
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
