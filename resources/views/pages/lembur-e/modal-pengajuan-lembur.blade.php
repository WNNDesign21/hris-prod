<!-- modal Area -->
<div class="modal fade" id="modal-pengajuan-lembur">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {{-- <h4 class="modal-title">Pengajuan Lembur</h4> --}}
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('lembure.pengajuan-lembur.store') }}" method="POST"
                    enctype="multipart/form-data" id="form-pengajuan-lembur">
                    @csrf
                    <div class="form-group">
                        <div class="col-12">
                            <div class="box">
                                <div class="box-body">
                                    <h4 class="box-title">Surat Perintah Lembur</h4>
                                    <div class="box-controls pull-right">
                                        <button type="button"
                                            class="btn btn-success waves-effect btnAddDetailLembur"><i
                                                class="fas fa-plus"></i></button>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 col-8 p-0">
                                            <div class="form-group">
                                                <label for="jenis_hari">Jenis Hari</label>
                                                <select name="jenis_hari" id="jenis_hari" class="form-control"
                                                    style="width:100%;" required>
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
                                        <table class="table mb-0" id="table-detail-lembur">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th style="width: 35%;">Karyawan</th>
                                                    <th style="width: 20%;">Job Description</th>
                                                    <th style="width: 20%;">Rencana Mulai</th>
                                                    <th style="width: 20%;">Rencana Selesai</th>
                                                    <th style="width: 5%;">Aksi</th>
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
                    </div>
                </form>
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
