<!-- modal Area -->
<div class="modal fade" id="modal-input-seksi">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Seksi</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('master-data.seksi.store') }}" method="POST" enctype="multipart/form-data"
                        id="form-tambah-seksi">
                        @csrf
                        <label for="">Nama</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="nama_seksi" id="nama_seksi" class="form-control" required>
                        </div>
                        <label for="">Departemen</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="id_departemen" id="id_departemen" class="form-control" required>
                                <option value="">Pilih Departemen</option>
                                @foreach ($departemen as $dp)
                                    <option value="{{ $dp->id_departemen }}">{{ $dp->nama }}</option>
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
