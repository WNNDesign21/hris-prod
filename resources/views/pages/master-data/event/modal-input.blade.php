<!-- modal Area -->
<div class="modal fade" id="modal-input-event">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Event</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('master-data.event.store') }}" method="POST" enctype="multipart/form-data"
                        id="form-tambah-event">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="jenis">Jenis Event</label>
                                    <select name="jenis_event" id="jenis_event" class="form-control"
                                        style="width:100%;">
                                        <option value="">Pilih Jenis Event</option>
                                        <option value="CB">Cuti Bersama</option>
                                        <option value="LN">Libur Nasional</option>
                                        <option value="EP">Event Perusahaan</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="jenis">Keterangan</label>
                                    <input type="text" class="form-control" id="keterangan" name="keterangan">
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label for="tanggal_mulai">Tanggal Mulai</label>
                                            <input type="date" class="form-control" id="tanggal_mulai"
                                                name="tanggal_mulai">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label for="tanggal_selesai">Tanggal Selesai</label>
                                            <input type="date" class="form-control" id="tanggal_selesai"
                                                name="tanggal_selesai">
                                        </div>
                                    </div>
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
