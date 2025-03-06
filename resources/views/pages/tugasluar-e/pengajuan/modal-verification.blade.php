<!-- modal Area -->
<div class="modal fade" id="modal-verification">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Verifikasi Kilometer Kendaraan</h4>
                <button type="button" class="btn-close btnCloseVerification" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-verification">
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Nomor Polisi</label>
                                    <div class="row">
                                        <div class="col-3">
                                            <input type="hidden" name="id_tugasluarVerif" id="id_tugasluarVerif"
                                                class="form-control" required>
                                            <input type="text" name="kode_wilayahVerif" id="kode_wilayahVerif"
                                                placeholder="T" class="form-control" required>
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="nomor_polisiVerif" id="nomor_polisiVerif"
                                                placeholder="1234" class="form-control" required>
                                        </div>
                                        <div class="col-3">
                                            <input type="text" name="seri_akhirVerif" id="seri_akhirVerif"
                                                placeholder="TCF" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Kilometer (Km)</label>
                                    <input type="text" class="form-control" id="kilometerVerif" name="kilometerVerif"
                                        required>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="submit" class="btn btn-primary" id="btnSubmitVerification">
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
