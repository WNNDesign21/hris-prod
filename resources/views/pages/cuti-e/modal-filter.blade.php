<!-- modal Area -->
<div class="modal fade" id="modal-filter">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filter Data</h4>
                <button type="button" class="btn-close btnCloseFilter" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Nama</label>
                                <input type="text" name="filterNama" id="filterNama" class="form-control"
                                    placeholder="Masukkan Nama Karyawan" style="width: 100%;">
                            </div>
                            <div class="form-group">
                                <label for="filterDepartemen">Departemen</label>
                                <select name="filterDepartemen" id="filterDepartemen" class="form-control"
                                    style="width: 100%;">
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterJenisCuti">Jenis Cuti</label>
                                <select name="filterJenisCuti" id="filterJenisCuti" class="form-control"
                                    style="width: 100%;">
                                    <option value="">Pilih Jenis Cuti</option>
                                    <option value="PRIBADI">PRIBADI</option>
                                    <option value="KHUSUS">KHUSUS</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterStatusCuti">Status Cuti</label>
                                <select name="filterStatusCuti" id="filterStatusCuti" class="form-control"
                                    style="width: 100%;">
                                    <option value="">Pilih Status Cuti</option>
                                    <option value="SCHEDULED">SCHEDULED</option>
                                    <option value="ON LEAVE">ON LEAVE</option>
                                    <option value="COMPLETED">COMPLETED</option>
                                    <option value="CANCELED">CANCELED</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterStatusDokumen">Status Dokumen</label>
                                <select name="filterStatusDokumen" id="filterStatusDokumen" class="form-control"
                                    style="width: 100%;">
                                    <option value="">Pilih Status Cuti</option>
                                    <option value="WAITING">WAITING</option>
                                    <option value="APPROVED">APPROVED</option>
                                    <option value="REJECTED">REJECTED</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterDurasi">Durasi</label>
                                <input type="number" name="filterDurasi" id="filterDurasi" class="form-control"
                                    placeholder="Masukkan Durasi" style="width: 100%;">
                            </div>
                            <div class="form-group">
                                <label for="filterRencanaMulai">Rencana Mulai Cuti</label>
                                <input type="month" name="filterRencanaMulai" id="filterRencanaMulai"
                                    class="form-control" style="width: 100%;">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-12 d-flex justify-content-end gap-1">
                        <button type="button" class="waves-effect waves-light btn btn-danger btnResetFilter"><i
                                class="fas fa-history"></i> Reset</button>
                        <button type="button" class="waves-effect waves-light btn btn-warning btnSubmitFilter"><i
                                class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</div>
<!-- /.modal -->
