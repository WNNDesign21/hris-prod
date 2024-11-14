<!-- modal Area -->
<div class="modal fade" id="modal-aktual-approval-lembur">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close btnCloseAktual" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data" id="form-aktual-approval-lembur">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <input type="hidden" name="id_lemburAktual" id="id_lemburAktual">
                        <div class="col-12">
                            <div class="box">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-center text-center">
                                            <h1 class="box-title">Surat Perintah Lembur</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <p>Status : <span id="statusAktual"></span></p>
                                        <p>Tanggal : <span id="text_tanggalAktual"></span></p>
                                        <p>Jenis Hari : <span id="jenis_hariAktual"></span></p>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body px-4 py-0">
                                    <div class="table-responsive">
                                        <table class="table mb-0" id="table-aktual-approval-lembur">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th style="width: 10%;">Karyawan</th>
                                                    <th style="width: 20%;">Job Description</th>
                                                    <th style="width: 5%;">Rencana Mulai</th>
                                                    <th style="width: 5%;">Rencana Selesai </th>
                                                    <th style="width: 10%;">Durasi (Rencana)</th>
                                                    <th style="width: 5%;">Aktual Mulai</th>
                                                    <th style="width: 5%;">Aktual Selesai</th>
                                                    <th style="width: 10%;">Durasi (Aktual)</th>
                                                    <th style="width: 15%;">Keterangan</th>
                                                    <th style="width: 15%;">Nominal</th>
                                                </tr>
                                            </thead>
                                            <tbody id="list-aktual-approval-lembur">
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
                            class="btn btn-success waves-effect btnUpdateAktualLembur">Update</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
