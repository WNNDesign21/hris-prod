<!-- modal Area -->
<div class="modal fade" id="modal-pengajuan-lembur">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Surat Perintah Lembur</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-pengajuan-lembur">
                        @csrf
                        <div class="row p-4">
                            <div class="col-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="karyawan">Karyawan</label>
                                                <select name="karyawan_id[]" id="karyawan_id_1" class="form-control"
                                                    style="width: 100%;">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <p><code>.box-header &gt; .box-title</code></p>
                                        <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis.
                                        </p>
                                    </div>
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
