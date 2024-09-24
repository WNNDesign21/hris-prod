<!-- modal Area -->
<div class="modal fade" id="modal-karyawan-pengganti">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pilih Karyawan Pengganti</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-karyawan-pengganti">
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <div class="col-12">
                                <input type="hidden" id="id_cuti" name="id_cuti">
                                <div class="form-group">
                                    <select class="form-control" name="karyawan_pengganti_id"
                                        id="karyawan_pengganti_id"></select>
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
