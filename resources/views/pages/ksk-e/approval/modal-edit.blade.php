<!-- modal Area -->
<div class="modal fade" id="modal-edit-device">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Device</h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data" id="form-edit-device">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="">Device Name</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="device_nameEdit" id="device_nameEdit" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Serial Number</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="device_snEdit" id="device_snEdit" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Cloud ID</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="cloud_idEdit" id="cloud_idEdit" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Server IP</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="server_ipEdit" id="server_ipEdit" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Server Port</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="server_portEdit" id="server_portEdit" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i>
                            Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
