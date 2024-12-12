<!-- modal Area -->
<div class="modal fade" id="modal-pengajuan-izin">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Pengajuan Izin</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('izine.pengajuan-izin.store') }}" method="POST"
                        enctype="multipart/form-data" id="form-pengajuan-izin">
                        @csrf
                        <div class="row p-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Jenis Izin<span class="text-danger">*</span></label>
                                    <select name="jenis_izin" id="jenis_izin" class="form-control" style="width: 100%;">
                                        <option value="TM">IZIN TIDAK MASUK</option>
                                        <option value="SH">IZIN 1/2 HARI</option>
                                        <option value="KP">IZIN KELUAR PABRIK</option>
                                        <option value="PL">IZIN PULANG</option>
                                    </select>
                                </div>
                                <div class="form-group" id="conditional_field">
                                    <div class="form-group">
                                        <label for="rencana_mulai_or_masuk" id="label_rencana_mulai_or_masuk">Rencana
                                            Mulai</label>
                                        <input type="date" name="rencana_mulai_or_masuk" id="rencana_mulai_or_masuk"
                                            class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="rencana_selesai_or_keluar"
                                            id="label_rencana_selesai_or_keluar">Rencana
                                            Selesai</label>
                                        <input type="date" name="rencana_selesai_or_keluar"
                                            id="rencana_selesai_or_keluar" class="form-control" required>
                                    </div>
                                    <small class="text-fade">Note : Jika izin hanya 1 hari, maka pilih Rencana Mulai dan
                                        Selesai di tanggal yang sama!</small>
                                </div>
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <textarea class="form-control" name="keterangan" id="keterangan" placeholder="Tulis keterangan izin disini..."
                                        style="width: 100%;" required></textarea>
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
