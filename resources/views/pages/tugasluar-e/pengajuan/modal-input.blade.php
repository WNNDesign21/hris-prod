<!-- modal Area -->
<div class="modal fade" id="modal-input">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pengajuan Tugas Luar</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('tugasluare.pengajuan.store') }}" method="POST" enctype="multipart/form-data"
                    id="form-tambah">
                    @csrf
                    <div class="form-group">
                        <label for="">Jam Keluar</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="time" name="jam_keluar" id="jam_keluar" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Jenis Kendaraan</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="jenis_kendaraan" id="jenis_kendaraan" class="form-control">
                                <option value="MOBIL">MOBIL</option>
                                <option value="MOTOR">MOTOR</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Kepemilikan Kendaraan</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="kepemilikan" id="kepemilikan" class="form-control">
                                <option value="OP">OPERASIONAL</option>
                                <option value="PR">PRIBADI</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Nomor Polisi</label>
                        <div class="row">
                            <div class="col-3">
                                <input type="text" name="kode_wilayah" id="kode_wilayah" class="form-control"
                                    required>
                            </div>
                            <div class="col-6">
                                <input type="text" name="nomor_polisi" id="nomor_polisi" class="form-control"
                                    required>
                            </div>
                            <div class="col-3">
                                <input type="text" name="seri_akhir" id="seri_akhir" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success waves-effect btnAddUrutan"><i
                                class="fas fa-plus"></i>&nbsp;&nbsp;Tambah Pengikut</button>
                    </div>
                    <div class="form-group">
                        <p class="text-fade">Note : Pengemudi & Pembuat TL tidak perlu diinput disini.</p>
                    </div>
                    <div class="row" id="list-pengikut">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
