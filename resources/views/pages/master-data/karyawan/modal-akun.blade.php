<!-- modal Area -->
<div class="modal fade" id="modal-akun">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="akun-title"></h4>
                <button type="button" class="btn-close btnCloseAkun" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('master-data.akun.storeUpdate') }}" method="POST" enctype="multipart/form-data"
                    id="form-akun">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" name="id_akunEdit" id="id_akunEdit">
                                <input type="hidden" name="id_karyawanAkunEdit" id="id_karyawanAkunEdit">
                                <div class="form-group">
                                    <label for="">Email Akun <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="email" name="email_akunEdit" id="email_akunEdit"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Username <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" name="username_akunEdit" id="username_akunEdit"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Password <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="password" name="password_akunEdit" id="password_akunEdit"
                                            class="form-control">
                                    </div>
                                </div>
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
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
