<div class="modal fade" id="modal-download-rekap" tabindex="-1" role="dialog" aria-labelledby="modal-download-rekap-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-download-rekap-label">Pilih Periode Rekap Manpower</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="rekap-period">Periode (Bulan Tahun):</label>
                    <input type="month" class="form-control" id="rekap-period" value="{{ \Carbon\Carbon::now()->format('Y-m') }}">
                </div>
                <div class="form-group">
                    <label for="organisasi-rekap">Organisasi</label>
                    <select class="form-control" id="organisasi-rekap">
                        <option value="all">All</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnConfirmDownloadRekap">Download</button>
            </div>
        </div>
    </div>
</div>