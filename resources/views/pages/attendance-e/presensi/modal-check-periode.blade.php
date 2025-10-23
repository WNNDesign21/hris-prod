<div class="modal fade" id="modal-check-periode">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pilih Periode Pengecekan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Pilih rentang tanggal untuk memeriksa data presensi terbaru dari mesin absensi.</p>
                <div class="form-group">
                    <label for="checkStartDate" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="checkStartDate" name="checkStartDate" value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label for="checkEndDate" class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="checkEndDate" name="checkEndDate" value="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSubmitCheckPeriode">
                    <i class="fas fa-search"></i>
                    Cari Data Baru
                </button>
            </div>
        </div>
    </div>
</div>