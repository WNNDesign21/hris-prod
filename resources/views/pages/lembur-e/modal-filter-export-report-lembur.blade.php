<!-- modal Area -->
<div class="modal fade" id="modal-filter">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filter Lembur</h4>
                <button type="button" class="btn-close btnCloseFilter" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="filterStatus">STATUS</label>
                                <select class="form-control" name="filterStatus" id="filterStatus" style="width: 100%;">
                                    <option value="">Pilih Status...</option>
                                    <option value="IP">IN PROGRESS</option>
                                    <option value="CO">COMPLETED</option>
                                    <option value="FL">FAILED</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterDepartemen">DEPARTEMEN</label>
                                <select class="form-control" name="filterDepartemen" id="filterDepartemen"
                                    style="width: 100%;">
                                    <option value="">Pilih Departemen...</option>
                                    @foreach ($departments as $item)
                                        <option value="{{ $item->id_departemen }}">{{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterPeriode">PERIODE</label>
                                <input type="month" class="form-control" name="filterPeriode" id="filterPeriode">
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
                                class="fas fa-filter"></i> Apply</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</div>
<!-- /.modal -->
