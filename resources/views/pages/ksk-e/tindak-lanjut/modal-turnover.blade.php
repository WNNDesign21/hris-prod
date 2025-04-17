<div class="modal fade" id="modal-turnover">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Karyawan Keluar</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('ksk.tindak-lanjut.store-turnover') }}" method="POST"
                    enctype="multipart/form-data" id="form-turnover">
                    @csrf
                    <div class="form-group">
                        <label for="">Karyawan <span class="text-danger">*</span></label>
                        <input type="hidden" name="id_ksk_detailTurnover" id="id_ksk_detailTurnover">
                        <select class="form-control" id="karyawan_idTurnover" name="karyawan_idTurnover"
                            style="width: 100%;" required>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Status Karyawan <span class="text-danger">*</span></label>
                        <select name="status_karyawanTurnover" id="status_karyawanTurnover" class="form-control"
                            style="width: 100%;" required>
                            <option value="MD">MENGUNDURKAN DIRI</option>
                            <option value="HK">HABIS KONTRAK</option>
                            <option value="PS">PENSIUN</option>
                            <option value="TM">TERMINASI</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tanggal Keluar</label>
                        <input type="date" name="tanggal_keluarTurnover" id="tanggal_keluarTurnover"
                            class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="">Keterangan</label>
                        <textarea name="keteranganTurnover" id="keteranganTurnover" class="form-control" required></textarea>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                            Submit</button>
                    </div>
                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</div>
<!-- /.modal -->
