<!-- modal Area -->
<div class="modal fade" id="modal-edit">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Pengajuan TL</h4>
                <button type="button" class="btn-close btnCloseEdit" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data" id="form-edit">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="">Jam Pergi</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="hidden" name="id_tugasluarEdit" id="id_tugasluarEdit" class="form-control">
                            <input type="time" name="jam_pergiEdit" id="jam_pergiEdit" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Jam Kembali</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="time" name="jam_kembaliEdit" id="jam_kembaliEdit" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Jenis Kendaraan</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="jenis_kendaraanEdit" id="jenis_kendaraanEdit" class="form-control"
                                style="width:100%;">
                                <option value="MOBIL">MOBIL</option>
                                <option value="MOTOR">MOTOR</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Kepemilikan Kendaraan</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="kepemilikan_kendaraanEdit" id="kepemilikan_kendaraanEdit" class="form-control"
                                style="width:100%;">
                                <option value="OP">OPERASIONAL</option>
                                <option value="OJ">OPERASIONAL JABATAN</option>
                                <option value="PR">PRIBADI</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Pengemudi</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="pengemudiEdit" id="pengemudiEdit" class="form-control" style="width:100%;">
                                @foreach ($karyawans as $item)
                                    <option value="{{ $item->id_karyawan }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Nomor Polisi</label>
                        <div class="row">
                            <div class="col-3">
                                <input type="text" name="kode_wilayahEdit" id="kode_wilayahEdit" class="form-control"
                                    required>
                            </div>
                            <div class="col-6">
                                <input type="text" name="nomor_polisiEdit" id="nomor_polisiEdit" class="form-control"
                                    required>
                            </div>
                            <div class="col-3">
                                <input type="text" name="seri_akhirEdit" id="seri_akhirEdit" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Rute</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="text" name="tempat_asalEdit" id="tempat_asalEdit" class="form-control"
                                    required>
                            </div>
                            <div class="col-6">
                                <input type="text" name="tempat_tujuanEdit" id="tempat_tujuanEdit"
                                    class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Keterangan / Uraian Keperluan</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <textarea name="keteranganEdit" id="keteranganEdit" class="form-control" style="width:100%;"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success waves-effect btnAddPengikutEdit"><i
                                class="fas fa-plus"></i>&nbsp;&nbsp;Tambah Pengikut</button>
                    </div>
                    <div class="form-group">
                        <p class="text-fade">Note : Pengemudi & Pembuat TL tidak perlu diinput disini.</p>
                    </div>
                    <div class="row" id="list-pengikutEdit">
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
