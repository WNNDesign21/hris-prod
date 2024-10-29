<!-- modal Area -->
<div class="modal fade" id="modal-detail-approval-lembur">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close btnCloseDetail" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-center text-center">
                                        <h1 class="box-title">Surat Perintah Lembur</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <p>Status : <span id="statusDetail"></span></p>
                                    <p>Tanggal : <span id="text_tanggalDetail"></span></p>
                                    <p>Jenis Hari : <span id="jenis_hariDetail"></span></p>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body px-4 py-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0" id="table-detail-approval-lembur">
                                        <thead class="table-primary">
                                            <tr>
                                                <th style="width: 20%;">Karyawan</th>
                                                <th style="width: 30%;">Job Description</th>
                                                <th style="width: 10%;">Rencana Mulai</th>
                                                <th style="width: 10%;">Rencana Selesai </th>
                                                <th style="width: 10%;">Aktual Mulai</th>
                                                <th style="width: 10%;">Aktual Selesai</th>
                                                <th style="width: 10%;">Durasi (Aktual)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="list-detail-approval-lembur">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
