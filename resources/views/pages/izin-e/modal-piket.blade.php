<!-- modal Area -->
<div class="modal fade" id="modal-piket">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Karyawan Piket</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('izine.piket.store') }}" method="POST" enctype="multipart/form-data"
                        id="form-piket">
                        @csrf
                        <div class="row p-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Karyawan<span class="text-danger">*</span></label>
                                    <select name="karyawan_id[]" id="karyawan_id" class="form-control"
                                        style="width: 100%;" multiple>
                                        @foreach ($karyawans as $item)
                                            <option value="{{ $item->id_karyawan }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="expired_date" id="label_expired_date">Expired Date</label>
                                    <input type="date" name="expired_date" id="expired_date" class="form-control"
                                        min="{{ date('Y-m-d') }}" required>
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
