<!-- modal Area -->
<div class="modal fade" id="modal-pengajuan-lembur">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pengajuan Lembur</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-pengajuan-lembur">
                        @csrf
                        <div class="col-12">
                            <div class="box">
                                <div class="box-body">
                                    <h4 class="box-title">Surat Perintah Lembur</h4>
                                    <div class="box-controls pull-right">
                                        <button type="button"
                                            class="btn btn-success waves-effect btnAddDetailLembur"><i
                                                class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body p-4">
                                    <div class="table-responsive">
                                        <table class="table mb-0" id="table-detail-lembur">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>Karyawan</th>
                                                    <th>Job Description</th>
                                                    <th>Rencana Mulai</th>
                                                    <th>Rencana Selesai</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="list-detail-lembur">
                                                {{-- <tr>
                                                    <td>
                                                        <select name="karyawan_id[]" id="karyawan_id"
                                                            class="form-control" style="width: 100%;">
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="job_description[]"
                                                            id="job_description" class="form-control"
                                                            style="width: 100%;">
                                                        </input>
                                                    </td>
                                                    <td>
                                                        <input type="datetime-local" name="rencana_mulai_lembur[]"
                                                            id="rencana_mulai_lembur" class="form-control"
                                                            style="width: 100%;">
                                                        </input>
                                                    </td>
                                                    <td>
                                                        <input type="datetime-local" name="rencana_selesai_lembur[]"
                                                            id="rencana_selesai_lembur" class="form-control"
                                                            style="width: 100%;">
                                                        </input>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button"
                                                                class="btn btn-danger waves-effect btnDelete"><i
                                                                    class="fas fa-trash"></i></button>
                                                        </div>
                                                    </td>
                                                </tr> --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary waves-effect btnSubmitDetailLembur"
                            disabled>Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
