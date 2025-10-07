<!-- modal Area -->
<div class="modal fade" id="modal-reject">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alasan Reject</h4>
                <button type="button" class="btn-close btnRejectClose" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-reject">
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <textarea class="form-control" name="rejected_note" id="rejected_note"
                                        placeholder="Tuliskan alasan menolak pengajuan lembur ini!" style="width: 100%;"></textarea>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="submit" class="btn btn-danger" id="btnSubmitReject"><i
                                            class="far fa-times-circle"></i>
                                        Reject</button>
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
