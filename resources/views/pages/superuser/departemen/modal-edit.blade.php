<!-- modal Area -->
<div class="modal fade" id="modal-edit-departemen">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Departemen</h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-edit-departemen">
                        @method('PATCH')
                        @csrf
                        <input type="hidden" name="id_departemen_edit" id="id_departemen_edit">
                        <label for="">Nama</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="nama_departemen_edit" id="nama_departemen_edit"
                                class="form-control">
                        </div>
                        <label for="">Divisi</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="id_divisi_edit" id="id_divisi_edit" class="form-control" required>
                                <option value="">Pilih Divisi</option>
                                @foreach ($divisi as $dv)
                                    <option value="{{ $dv->id_divisi }}">{{ $dv->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
