<!-- modal Area -->
<div class="modal fade" id="modal-show-qrcode">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Scan QR</h4>
                <button type="button" class="btn-close btnCloseQrcode" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <img id="qr-code" src="{{ asset('img/qrcode.png') }}" alt="QR Code" style="max-width: 100%;">
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <p class="text-center">Tunjukan QR Code ini di Post Security Ketika Hendak Masuk/Keluar Area Pabrik
                </p>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
