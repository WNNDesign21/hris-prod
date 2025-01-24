<!-- modal Area -->
<div class="modal fade" id="modal-filter-summary">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filter</h4>
                <button type="button" class="btn-close btnCloseFilterSummary" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12">
                            @if (auth()->user()->hasRole('personalia'))
                                <div class="form-group">
                                    <label for="filterDepartemenSummary">DEPARTEMEN</label>
                                    <select name="filterDepartemenSummary" id="filterDepartemenSummary"
                                        class="form-control" style="width: 100%;" multiple>
                                        @foreach ($departemens as $item)
                                            <option value="{{ $item->id_departemen }}"
                                                data-departemen="{{ $item->nama }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="filterTanggalSummary">TANGGAL</label>
                                <input type="date" class="form-control" id="filterTanggalSummary"
                                    name="filterTanggalSummary">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-12 d-flex justify-content-end gap-1">
                        <button type="button" class="waves-effect waves-light btn btn-danger btnResetFilterSummary"><i
                                class="fas fa-history"></i> Reset</button>
                        <button type="button"
                            class="waves-effect waves-light btn btn-warning btnSubmitFilterSummary"><i
                                class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</div>
<!-- /.modal -->
