<!-- modal Area -->
<div class="modal fade" id="modal-approval-lembur">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {{-- <h4 class="modal-title">Pengajuan Lembur</h4> --}}
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-body">
                                <h4 class="box-title">Surat Perintah Lembur (Detail)</h4>
                                <div class="row">
                                    <div class="col-lg-4 col-8 p-0">
                                        <div class="form-group">
                                            <label for="jenis_hari">Jenis Hari</label>
                                            <input type="text" class="form-control" id="jenis_hari">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body px-4 py-0">
                                <div class="table-responsive">
                                    <table class="table mb-0" id="table-approval-detail-lembur">
                                        <thead class="table-primary">
                                            <tr>
                                                <th style="width: 20%;">Karyawan</th>
                                                <th style="width: 35%;">Job Description</th>
                                                <th style="width: 20%;">Rencana Mulai </th>
                                                <th style="width: 20%;">Rencana Selesai </th>
                                                <th style="width: 5%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="list-approval-detail-lembur">
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
