<!-- modal Area -->
<div class="modal fade" id="modal-detail-review-lembur">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detail Lembur</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-12">
                    <div class="row">
                        <p>Departemen : <span id="detailReviewDepartemen"></span></p>
                        <p>Status : <span id="detailReviewStatus"></span></p>
                        <p>Tanggal : <span id="detailReviewTanggal"></span></p>
                    </div>
                </div>
                <hr>
                <div class="table-responsive">
                    <table id="detail-review-table" class="table table-striped table-bordered display nowrap"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>ID Lembur</th>
                                <th>Karyawan</th>
                                <th>Job Description</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Keterangan</th>
                                <th>Nominal</th>
                                <th>Chekced</th>
                                <th>Approved</th>
                            </tr>
                        </thead>
                        <tbody id="detailReviewContent"></tbody>
                    </table>
                </div>
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</div>
<!-- /.modal -->
