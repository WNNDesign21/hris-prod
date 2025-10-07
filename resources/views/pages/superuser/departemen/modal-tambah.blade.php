<!-- modal Area -->
<div class="modal fade" id="modal-input-departemen">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Departemen</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('superuser.departemen.store') }}" method="POST"
                        enctype="multipart/form-data" id="form-tambah-departemen">
                        @csrf
                        <label for="">Nama</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="nama_departemen" id="nama_departemen" class="form-control"
                                required>
                        </div>
                        <label for="">Divisi</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="id_divisi" id="id_divisi" class="form-control" required>
                                <option value="">Pilih Divisi</option>
                                @foreach ($divisi as $dv)
                                    <option value="{{ $dv->id_divisi }}">{{ $dv->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                Tambah</button>
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
