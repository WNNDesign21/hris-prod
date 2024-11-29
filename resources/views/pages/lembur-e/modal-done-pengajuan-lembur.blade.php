<!-- modal Area -->
<div class="modal fade" id="modal-detail-lembur-done">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close btnCloseDone" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data" id="form-detail-lembur-done"
                    class="dropzone" style="border:none!important;">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <input type="hidden" name="id_lemburDone" id="id_lemburDone">
                        <div class="col-12">
                            <div class="box">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-center text-center">
                                            <h1 class="box-title">Surat Perintah Lembur</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <p>Status : <span id="statusDone"></span></p>
                                        <p>Tanggal : <span id="text_tanggalDone"></span></p>
                                        <p>Jenis Hari : <span id="jenis_hariDone"></span></p>
                                        <div class="col-12 mb-4">
                                            <p class="fw-bold">Lampiran LKH</p>
                                            <div class="row d-inline-block previewAttachmentLembur">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-12">
                                            <input type="file" class="form-control" name="attachment_lembur"
                                                id="attachment_lembur">
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body px-1 py-0">
                                    <div class="row" id="list-detail-lembur-done">
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-success waves-effect btnSubmitDoneLembur">Done</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
