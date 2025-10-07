<!-- modal Area -->
<div class="modal fade" id="modal-upload-shiftgroup">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Upload Shift Group</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('attendance.shiftgroup.store') }}" method="POST" enctype="multipart/form-data"
                    id="form-upload-shiftgroup">
                    @csrf
                    <div class="form-group">
                        <label for="">File</label>
                        <input type="file" name="file" id="file" class="form-control" style="width: 100%;"
                            required>
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
