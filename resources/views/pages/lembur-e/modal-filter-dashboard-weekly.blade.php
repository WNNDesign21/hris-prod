<!-- modal Area -->
<div class="modal fade" id="modal-filter-weekly">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filter Weekly</h4>
                <button type="button" class="btn-close btnCloseFilterWeekly" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12">
                            @if (auth()->user()->hasRole('personalia'))
                                <div class="form-group">
                                    <label for="filterDepartemenWeekly">DEPARTEMEN</label>
                                    <select name="filterDepartemenWeekly" id="filterDepartemenWeekly"
                                        class="form-control" style="width: 100%;" multiple>
                                        @foreach ($departemens as $item)
                                            <option value="{{ $item->id_departemen }}"
                                                data-departemen="{{ $item->nama }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="filterPeriodeWeekly">PERIODE</label>
                                <input type="month" class="form-control" id="filterPeriodeWeekly"
                                    name="filterPeriodeWeekly">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-12 d-flex justify-content-end gap-1">
                        <button type="button" class="waves-effect waves-light btn btn-danger btnResetFilterWeekly"><i
                                class="fas fa-history"></i> Reset</button>
                        <button type="button" class="waves-effect waves-light btn btn-warning btnSubmitFilterWeekly"><i
                                class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</div>
<!-- /.modal -->
