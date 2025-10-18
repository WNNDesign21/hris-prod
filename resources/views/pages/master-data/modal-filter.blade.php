<div class="modal fade" id="modal-filter-current" tabindex="-1" aria-labelledby="modalFilterCurrentLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFilterTitle">Filter Desa</h5>
                <button type="button" class="btn-close closeFilterCurrent" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="filterSumber" class="form-label fw-bold">Sumber Data</label>
                    <select name="filterSumber" id="filterSumber" class="form-control" style="width: 100%;">
                        <option value="">Pilih Sumber</option>
                        <option value="Domisili">Domisili</option>
                        <option value="Alamat">Alamat</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger btnResetFilterCurrent">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="button" class="btn btn-warning btnSubmitFilterCurrent">
                    <i class="fas fa-filter"></i> Filter
                </button>


            </div>
        </div>
    </div>
</div>