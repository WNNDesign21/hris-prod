<!-- modal Area -->
<div class="modal fade" id="modal-input">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Presense <span id="camera-previewText"></span></h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <video id="camera-preview" style="width: 100%;" autoplay playsinline></video>
                <div class="d-flex justify-content-center gap-1">
                    <button type="button" class="btn btn-primary btnSwitch">Switch Camera</button>
                    <button type="button" class="btn btn-success btnCapture">Capture</button>
                </div>
            </div>
        </div>
    </div>
</div>
