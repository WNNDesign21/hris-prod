<!-- modal Area -->
<div class="modal fade" id="modal-pengajuan-lembur">
    <div class="modal-dialog modal-fullscreen" role="document">
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
                            <div class="d-flex justify-content-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success waves-effect btnAdd"><i
                                            class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="row p-4">
                            <table class="table table-stripped">
                                <thead>
                                    <th>Karyawan</th>
                                    <th>Job Description</th>
                                    <th>Rencana Mulai</th>
                                    <th>Rencana Selesai</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="karyawan_id[]" id="karyawan_id" class="form-control"
                                                style="width: 100%;">
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="job_description[]" id="job_description"
                                                class="form-control" style="width: 100%;">
                                            </input>
                                        </td>
                                        <td>
                                            <input type="datetime-local" name="rencana_mulai_lembur[]"
                                                id="rencana_mulai_lembur" class="form-control" style="width: 100%;">
                                            </input>
                                        </td>
                                        <td>
                                            <input type="datetime-local" name="rencana_selesai_lembur[]"
                                                id="rencana_selesai_lembur" class="form-control" style="width: 100%;">
                                            </input>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-danger waves-effect btnDelete"><i
                                                        class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
