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
                        <div class="form-group">
                            <label for="">No Label</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="text" name="no_label_edit" id="no_label_edit" class="form-control"
                                    readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="part_code" class="form-label">Search Product</label>
                            <select class="form-select" name="product_id_edit" id="product_id_edit"
                                style="width: 100%;">
                                <option selected value="">Cari Product disini...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="customer" class="form-label">Customer</label>
                            <select class="form-select" name="customer_edit" id="customer_edit" style="width: 100%;"
                                select2>
                                <option selected value="" disabled>Pilih Customer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="identitas_lot_edit" class="form-label">Identitas (LOT)</label>
                            <input type="name" class="form-control" id="identitas_lot_edit"
                                name="identitas_lot_edit">
                        </div>
                        <div class="form-group">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="text" class="form-control" placeholder="Insert "id="quantity_edit"
                                name="quantity_edit" value="0">
                        </div>
                        <div class="col-12 d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-block btn-warning" style="width: 100%;"><i
                                    class="glyphicon glyphicon-ok-sign"></i> Update</button>
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
