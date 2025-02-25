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
                        <label for="">Jenis Kepemilikan</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="jenis_kepemilikan" id="jenis_kepemilikan" class="form-control"
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
                                    <option value="{{ $item->id_karyawan }}"
                                        {{ auth()->user()->karyawan->id_karyawan == $item->id_karyawan ? 'selected' : '' }}>
                                        {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Jenis Keberangkatan</label>
                        <div class="input-group mb-2" style="width:100%;">
                            <select name="jenis_keberangkatan" id="jenis_keberangkatan" class="form-control"
                                style="width:100%;">
                                <option value="KTR" selected>KANTOR</option>
                                <option value="RMH">RUMAH</option>
                                <option value="LNA">LAINNYA</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Rute</label>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-floating">
                                    <input type="text" name="tempat_asal" id="tempat_asal" class="form-control"
                                        required>
                                    <label for="tempat_asal">Tempat Asal</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating">
                                    <input type="text" name="tempat_tujuan" id="tempat_tujuan" class="form-control"
                                        required>
                                    <label for="tempat_tujuan">Tempat Tujuan</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="conditional-field">
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
