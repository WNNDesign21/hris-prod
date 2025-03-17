<!-- modal Area -->
<div class="modal fade" id="modal-input-device">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Device</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('attendance.device.store') }}" method="POST" enctype="multipart/form-data"
                    id="form-tambah-device">
                    @csrf
                    <div class="form-group">
                        <label for="">Device Name</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="device_name" id="device_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Serial Number</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="device_sn" id="device_sn" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Cloud ID</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="cloud_id" id="cloud_id" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Server IP</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="server_ip" id="server_ip" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Server Port</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="server_port" id="server_port" class="form-control" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                            Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
