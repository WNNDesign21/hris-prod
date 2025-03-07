<!-- modal Area -->
<div class="modal fade" id="modal-filter-review-lembur">
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
                                <label for="filterPeriode">PERIODE</label>
                                <input type="month" class="form-control" id="filterPeriode" name="filterPeriode" />
                            </div>
                            <div class="form-group">
                                <label for="filterDepartemen">DEPARTEMEN</label>
                                <select name="filterDepartemen" id="filterDepartemen" class="form-control"
                                    style="width: 100%;" multiple>
                                    @foreach ($departemens as $item)
                                        <option value="{{ $item->id_departemen }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterOrganisasi">PLANT</label>
                                <select name="filterOrganisasi" id="filterOrganisasi" class="form-control"
                                    style="width: 100%;" multiple>
                                    @foreach ($organisasis as $item)
                                        <option value="{{ $item->id_organisasi }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterStatus">STATUS</label>
                                <select name="filterStatus" id="filterStatus" class="form-control" style="width: 100%;">
                                    <option value="PLANNING">PLANNING</option>
                                    <option value="ACTUAL">ACTUAL</option>
                                </select>
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
