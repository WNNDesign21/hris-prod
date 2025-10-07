<!-- modal Area -->
<div class="modal fade" id="modal-filter">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filter Detail Lembur</h4>
                <button type="button" class="btn-close btnCloseFilter" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="filterData">DATA</label>
                                <select name="filterData" id="filterData" class="form-control" style="width: 100%;">
                                    <option value="10">TOP 10</option>
                                    <option value="25">TOP 25</option>
                                    <option value="50" selected>TOP 50</option>
                                    <option value="100">TOP 100</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterDepartemen">DEPARTEMEN</label>
                                <select name="filterDepartemen" id="filterDepartemen" class="form-control"
                                    style="width: 100%;">
                                    <option value="" data-departemen="SEMUA DEPARTEMEN">SEMUA DEPARTEMEN</option>
                                    @foreach ($departemens as $item)
                                        <option value="{{ $item->id_departemen }}"
                                            data-departemen="{{ $item->nama }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterPeriode">PERIODE</label>
                                <input type="month" class="form-control" id="filterPeriode" name="filterPeriode">
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
