<!-- modal Area -->
<div class="modal fade" id="modal-pengajuan-izin-edit">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Pengajuan Izin</h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-pengajuan-izin-edit">
                        @csrf
                        @method('PATCH')
                        <div class="row p-4">
                            <div class="col-12">
                                <input type="hidden" id="id_izinEdit" name="id_izinEdit">
                                <div class="form-group" id="conditional_fieldEdit">
                                </div>
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <textarea class="form-control" name="keteranganEdit" id="keteranganEdit" placeholder="Tulis keterangan izin disini..."
                                        style="width: 100%;" required></textarea>
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
