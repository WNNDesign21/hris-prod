<!-- modal Area -->
<div class="modal fade" id="modal-export">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Export Lembur</h4>
                <button type="button" class="btn-close btnCloseExport" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data" id="form-export">
                    @csrf
                    <div class="form-group">
                        <label for="">Start Date</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="date" name="start_dateExport" id="start_dateExport" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">End Date</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="date" name="end_dateExport" id="end_dateExport" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-success">
                                <i class="far fa-file-excel"></i> Export</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
