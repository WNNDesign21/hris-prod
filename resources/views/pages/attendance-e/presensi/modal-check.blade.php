<!-- modal Area -->
<div class="modal fade" id="modal-check">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title detailText">Check Presensi</h4>
                <button type="button" class="btn-close btnCloseCheck" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="check-table" class="table table-striped table-bordered display nowrap"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="checkContent"></tbody>
                    </table>
                </div>
                <div class="row">
                    <input type="hidden" id="id">
                    <input type="hidden" id="date">
                    <input type="hidden" id="type">
                    @role('personalia')
                        <div class="col text-end">
                            <button type="button" class="btn btn-light btnReset">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    @endrole
                </div>
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</div>
<!-- /.modal -->
