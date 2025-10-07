<!-- modal Area -->
<div class="modal fade" id="modal-reject-cuti">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alasan Reject</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-reject-cuti">
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <div class="col-12">
                                <input type="hidden" id="id_cuti" name="id_cuti">
                                <input type="hidden" id="nama_atasan" name="nama_atasan">
                                <div class="form-group">
                                    <textarea class="form-control" name="alasan_reject" id="alasan_reject"
                                        placeholder="Tuliskan alasan menolak pengajuan cuti ini!" style="width: 100%;"></textarea>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="submit" class="btn btn-danger"><i class="fas fa-save"></i>
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
