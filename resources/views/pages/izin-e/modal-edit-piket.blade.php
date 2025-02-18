<!-- modal Area -->
<div class="modal fade" id="modal-piket-edit">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Karyawan Piket</h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-piket-edit">
                        @csrf
                        @method('PATCH')
                        <div class="row p-4">
                            <div class="col-12">
                                <input type="hidden" id="id_piketEdit" name="id_piketEdit">
                                <div class="form-group">
                                    <label for="">Karyawan</label>
                                    <select name="karyawan_idEdit" id="karyawan_idEdit" class="form-control"
                                        style="width:100%;">
                                        @foreach ($karyawans as $item)
                                            <option value="{{ $item->id_karyawan }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Expired Date</label>
                                    <input type="date" class="form-control" name="expired_dateEdit"
                                        id="expired_dateEdit" required></input>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i>
                                        Submit</button>
                                </div>
                            </div>
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
