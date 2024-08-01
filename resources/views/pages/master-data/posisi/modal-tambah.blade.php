<!-- modal Area -->
<div class="modal fade" id="modal-input-posisi">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Posisi</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('master-data.posisi.store') }}" method="POST" enctype="multipart/form-data"
                        id="form-tambah-posisi">
                        @csrf
                        <label for="">Nama</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="text" name="nama_posisi" id="nama_posisi" class="form-control" required>
                        </div>
                        <label for="">Jabatan</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="id_jabatan" id="id_jabatan" class="form-control select2" required>
                                <option value="">Pilih Jabatan</option>
                                @foreach ($jabatan as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
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
