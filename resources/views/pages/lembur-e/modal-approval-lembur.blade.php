<!-- modal Area -->
<div class="modal fade" id="modal-approval-lembur">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data" id="form-approval-lembur">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <input type="hidden" name="id_lembur" id="id_lembur">
                        <div class="col-12">
                            <div class="box">
                                <div class="box-body">
                                    <h4 class="box-title">Surat Perintah Lembur</h4>
                                    <div class="row">
                                        <div class="col-lg-4 col-8">
                                            <div class="form-group">
                                                <label for="jenis_hari">Jenis Hari</label>
                                                <select name="jenis_hari" id="jenis_hari" class="form-control"
                                                    style="width:100%;" disabled>
                                                    <option value="">PILIH JENIS HARI LEMBUR</option>
                                                    <option value="WE">WEEKEND</option>
                                                    <option value="WD">WEEKDAY</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body px-4 py-0">
                                    <div class="table-responsive">
                                        <table class="table mb-0" id="table-approval-lembur">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th style="width: 20%;">Karyawan</th>
                                                    <th style="width: 30%;">Job Description</th>
                                                    <th style="width: 20%;">Rencana Mulai </th>
                                                    <th style="width: 20%;">Rencana Selesai </th>
                                                    <th style="width: 5%;">Durasi</th>
                                                    <th style="width: 5%;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="list-approval-lembur">
                                            </tbody>
                                        </table>
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
                        <button type="button"
                            class="btn btn-warning waves-effect btnUpdateStatusDetailLembur">Update</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
