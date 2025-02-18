<!-- modal Area -->
<div class="modal fade" id="modal-edit">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Device</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data" id="form-edit">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="">Device Name</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="device_name" id="device_name" class="form-control" required>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
