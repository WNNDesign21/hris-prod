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
                                    <label for="kilometer" id="status"></label>
                                    <input type="text" class="form-control" name="kilometer" id="kilometer"
                                        placeholder="Isi dengan angka saja (Satuan KM)" style="width: 100%;">
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
