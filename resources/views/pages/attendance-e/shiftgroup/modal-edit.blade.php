<!-- modal Area -->
<div class="modal fade" id="modal-edit-shiftgroup">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Shift Grop <span id="karyawan"></span></h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data" id="form-edit-shiftgroup">
                    @csrf
                    @method('PATCH')
                    {{-- <div class="form-group">
                        <label for="pin_edit">PIN</label>
                        <input type="numeric" name="pin_edit" id="pin_edit" class="form-control" style="width: 100%;"
                            min="0" required>
                    </div> --}}
                    <div class="form-group">
                        <label for="grup_edit">Shift Group Name</label>
                        <select name="grup_edit" id="grup_edit" class="form-control" style="width: 100%;" required>
                            <option value="">Pilih Shift Group</option>
                            @foreach ($grups as $grup)
                                <option value="{{ $grup->id_grup }}">
                                    {{ $grup->nama . ' (' . \Carbon\Carbon::parse($grup->jam_masuk)->format('H:i') . ' - ' . \Carbon\Carbon::parse($grup->jam_keluar)->format('H:i') . ')' }}
                                </option>
                            @endforeach
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
