<!-- modal Area -->
<div class="modal fade" id="modal-edit-shiftgroup">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Shift Group <span id="karyawan"></span></h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data" id="form-edit-shiftgroup">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="grup_pattern_edit">Shift Pattern</label>
                        <select name="grup_pattern_edit" id="grup_pattern_edit" class="form-control"
                            style="width: 100%;" required>
                            <option value="">Pilih Pola Shift</option>
                            @foreach ($grup_patterns as $gp)
                                <option value="{{ $gp->id_grup_pattern }}">
                                    {{ $gp->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group d-none" id="current_shift">
                        <label for="grup_edit">Current Shift</label>
                        <select name="grup_edit" id="grup_edit" class="form-control" style="width: 100%;" required>
                            <option value="">Pilih Shift</option>
                        </select>
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
