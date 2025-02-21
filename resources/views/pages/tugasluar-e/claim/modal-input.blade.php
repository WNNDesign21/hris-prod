<!-- modal Area -->
<div class="modal fade" id="modal-input">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Claim TL</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('tugasluare.claim.store') }}" method="POST" enctype="multipart/form-data"
                    id="form-input">
                    @csrf
                    <div class="form-group">
                        <label for="">Jam Pergi</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="time" name="jam_pergi" id="jam_pergi" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Jam Kembali</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <input type="time" name="jam_kembali" id="jam_kembali" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Jenis Kendaraan</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="jenis_kendaraan" id="jenis_kendaraan" class="form-control"
                                style="width:100%;">
                                <option value="MOBIL">MOBIL</option>
                                <option value="MOTOR">MOTOR</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Kepemilikan Kendaraan</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="kepemilikan_kendaraan" id="kepemilikan_kendaraan" class="form-control"
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
                            <select name="pengemudi" id="pengemudi" class="form-control" style="width:100%;">
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
                        <label for="">Rute</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="text" name="tempat_asal" id="tempat_asal" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <input type="text" name="tempat_tujuan" id="tempat_tujuan" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Keterangan / Uraian Keperluan</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <textarea name="keterangan" id="keterangan" class="form-control" style="width:100%;"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success waves-effect btnAddPengikut"><i
                                class="fas fa-plus"></i>&nbsp;&nbsp;Tambah Pengikut</button>
                    </div>
                    <div class="form-group">
                        <p class="text-fade">Note : Pengemudi tetap harus diinput sebagai pengikut.</p>
                    </div>
                    <div class="row" id="list-pengikut">
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
