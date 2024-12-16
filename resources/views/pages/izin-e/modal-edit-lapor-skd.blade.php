<!-- modal Area -->
<div class="modal fade" id="modal-lapor-skd-edit">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Lapor SKD</h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-lapor-skd-edit">
                        @csrf
                        @method('PATCH')
                        <div class="row p-4">
                            <div class="col-12">
                                <input type="hidden" id="id_sakitEdit" name="id_sakitEdit">
                                <div class="form-group">
                                    <label for="tanggal_mulaiEdit" id="label_tanggal_mulai">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulaiEdit" id="tanggal_mulaiEdit"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_selesaiEdit" id="label_tanggal_selesaiEdit">Tanggal
                                        Selesai</label>
                                    <input type="date" name="tanggal_selesaiEdit" id="tanggal_selesaiEdit"
                                        class="form-control">
                                </div>
                                <a id="linkFotoEdit" href="#" class="image-popup-vertical-fit"
                                    data-title="Lampiran SKD">
                                    <img id="imageReviewEdit" src="#" alt="Image Foto"
                                        style="width: 150px;height: 150px;" class="img-fluid">
                                </a>
                                <div class="form-group">
                                    <label for="">Lampiran SKD</label>
                                    <br>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary" id="btnUploadLampiranSkdEdit"><i
                                                class="fas fa-upload"></i> Upload</button>
                                        <button type="button" class="btn btn-danger" id="btnResetLampiranSkdEdit"><i
                                                class="fas fa-trash"></i> Reset</button>
                                    </div>
                                    <input type="file" name="lampiran_skdEdit" id="lampiran_skdEdit"
                                        class="form-control" style="display: none;">
                                </div>
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <textarea class="form-control" name="keteranganEdit" id="keteranganEdit"
                                        placeholder="Tulis keterangan sakit jika diperlukan..." style="width: 100%;"></textarea>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                        Update</button>
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
