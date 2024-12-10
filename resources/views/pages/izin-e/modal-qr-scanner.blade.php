<!-- modal Area -->
<div class="modal fade" id="modal-qr-scanner">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">QR Scanner</h4>
                <button type="button" class="btn-close btnCloseQrScanner" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="form-group">
                            <input type="file" class="form-control" id="qr-input-file" accept="image/*"
                                style="width:100%;">
                        </div>
                        <div id="qr-scanner" style="min-width: 350px; height:100%;" class="d-none"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <h5 class="text-center">Input QR Code disini</h5>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
