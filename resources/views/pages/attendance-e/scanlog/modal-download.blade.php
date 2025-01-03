<!-- modal Area -->
<div class="modal fade" id="modal-download-scanlog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Download Scanlog</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('attendance.scanlog.export-scanlog') }}" method="POST"
                    enctype="multipart/form-data" id="form-export-scanlog">
                    @csrf
                    <div class="form-group">
                        <label for="">Device</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="device_id" id="device_id" class="form-control" required style="width: 100%;">
                                @foreach ($devices as $device)
                                    <option value="{{ $device->id_device }}">{{ $device->device_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Format Export</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="format" id="format" class="form-control" required style="width: 100%;">
                                <option value="H">HORIZONTAL</option>
                                <option value="V">VERTICAL</option>
                            </select>
                        </div>
                        <span class="form-text text-muted mb-3">Note : Hanya digunakan untuk Export Scanlog</span>
                        <div class="form-group">
                            <label for="">Start Date</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="date" name="start_date" id="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">End Date</label>
                            <div class="input-group mb-2" style="width:100%;">
                                <input type="date" name="end_date" id="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btnGetScanlog">
                                    <i class="fas fa-fingerprint"></i> Get Scanlog</button>
                                <button type="button" class="btn btn-success btnExport">
                                    <i class="far fa-file-excel"></i> Export Scanlog</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>
