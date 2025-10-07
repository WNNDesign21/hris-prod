<!-- modal Area -->
<div class="modal fade" id="modal-aktual-pengajuan-izin">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Aktual Izin</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="#" method="POST" enctype="multipart/form-data" id="form-aktual-pengajuan-izin">
                        @csrf
                        @method('PATCH')
                        <div class="row p-4">
                            <div class="col-12">
                                <input type="hidden" id="id_izinAktual" name="id_izinAktuak">
                                <div class="form-group">
                                    <label for="aktual_mulai_or_masukAktual"
                                        id="label_aktual_mulai_or_masukAktual">Aktual Mulai</label>
                                    <input type="date" name="aktual_mulai_or_masukAktual"
                                        id="aktual_mulai_or_masukAktual" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="aktual_selesai_or_keluarAktual"
                                        id="label_aktual_selesai_or_keluarAktual">Aktual Selesai</label>
                                    <input type="date" name="aktual_selesai_or_keluarAktual"
                                        id="aktual_selesai_or_keluarAktual" class="form-control" required>
                                </div>
                                <small class="text-fade">Note : Jika izin hanya 1 hari, maka pilih Rencana Mulai dan
                                    Selesai di tanggal yang sama!</small>
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
