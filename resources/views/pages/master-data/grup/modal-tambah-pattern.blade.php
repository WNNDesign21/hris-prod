<!-- modal Area -->
<div class="modal fade" id="modal-input-shift-pattern">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Shift Pattern</h4>
                <button type="button" class="btn-close btnCloseSp" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('master-data.grup.store-shift-pattern') }}" method="POST"
                    enctype="multipart/form-data" id="form-tambah-shift-pattern">
                    @csrf
                    <div class="form-group">
                        <label for="">Nama Shift Pattern</label>
                        <input type="text" name="nama_shift_pattern" id="nama_shift_pattern" class="form-control"
                            style="width: 100%;" required>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success waves-effect btnAddUrutan"><i
                                class="fas fa-plus"></i>&nbsp;&nbsp;Tambah Pola</button>
                    </div>
                    <div class="form-group">
                        <p class="text-fade">Note : Urutan Shift Dari Atas ke Bawah</p>
                    </div>
                    <div class="row" id="list-urutan">
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                            Tambah</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
