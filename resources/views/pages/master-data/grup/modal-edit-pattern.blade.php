<!-- modal Area -->
<div class="modal fade" id="modal-edit-shift-pattern">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Shift Pattern</h4>
                <button type="button" class="btn-close btnCloseEditSp" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data" id="form-edit-shift-pattern">
                    @method('PATCH')
                    @csrf
                    <div class="form-group">
                        <label for="">Nama Shift Pattern</label>
                        <input type="hidden" name="id_shift_patternEdit" id="id_shift_patternEdit" class="form-control"
                            style="width: 100%;" required>
                        <input type="text" name="nama_shift_patternEdit" id="nama_shift_patternEdit"
                            class="form-control" style="width: 100%;" required>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success waves-effect btnAddUrutanEdit"><i
                                class="fas fa-plus"></i>&nbsp;&nbsp;Tambah Pola</button>
                    </div>
                    <div class="form-group">
                        <p class="text-fade">Note : Urutan Shift Dari Atas ke Bawah</p>
                    </div>
                    <div class="row" id="list-urutanEdit">
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i>
                            Update</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
