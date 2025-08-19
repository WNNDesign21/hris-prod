<!-- modal Area -->
<div class="modal fade" id="modal-filter-monthly">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filter Dashboard Monthly</h4>
                <button type="button" class="btn-close btnCloseFilterMonthly" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="filterOrganisasiMonthly">Organisasi</label>
                                <select name="filterOrganisasiMonthly" id="filterOrganisasiMonthly" class="form-control"
                                    style="width: 100%;" multiple>
                                    @foreach ($organisasis as $item)
                                        <option value="{{ $item->id_organisasi }}"
                                            data-departemen="{{ $item->nama }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (auth()->user()->hasAnyRole(['personalia', 'personalia-lembur']))
                                <div class="form-group">
                                    <label for="filterDepartemenMonthly">DEPARTEMEN</label>
                                    <select name="filterDepartemenMonthly" id="filterDepartemenMonthly"
                                        class="form-control" style="width: 100%;" multiple>
                                        @foreach ($departemens as $item)
                                            <option value="{{ $item->id_departemen }}"
                                                data-departemen="{{ $item->nama }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="filterTahunMonthly">TAHUN</label>
                                <select name="filterTahunMonthly" id="filterTahunMonthly" class="form-control"
                                    style="width: 100%;">
                                    @for ($year = date('Y'); $year >= date('Y') - 5; $year--)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
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
                        <button type="button" class="waves-effect waves-light btn btn-danger btnResetFilterMonthly"><i
                                class="fas fa-history"></i> Reset</button>
                        <button type="button"
                            class="waves-effect waves-light btn btn-warning btnSubmitFilterMonthly"><i
                                class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</div>
<!-- /.modal -->
