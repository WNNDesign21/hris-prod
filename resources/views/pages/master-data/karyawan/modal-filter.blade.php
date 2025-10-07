<!-- modal Area -->
<div class="modal fade" id="modal-filter">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filter Data</h4>
                <button type="button" class="btn-close btnCloseFilter" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <div class="form-group">
                                <label for="">Nomor Induk Karyawan</label>
                                <input type="text" name="filterNik" id="filterNik" class="form-control"
                                    placeholder="Masukkan Nomor Induk Karyawan" style="width: 100%;">
                            </div>
                            <div class="form-group">
                                <label for="">Nama</label>
                                <input type="text" name="filterNama" id="filterNama" class="form-control"
                                    placeholder="Masukkan Nama Karyawan" style="width: 100%;">
                            </div>
                            <div class="form-group">
                                <label for="filterDepartemen">Departemen</label>
                                <select name="filterDepartemen" id="filterDepartemen" class="form-control"
                                    style="width: 100%;">
                                    <option value="">Pilih Departemen</option>
                                    @foreach ($departemen as $dp)
                                        <option value="{{ $dp->id_departemen }}">{{ $dp->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterStatuskaryawan">Status Karyawan</label>
                                <select name="filterStatuskaryawan" id="filterStatuskaryawan" class="form-control"
                                    style="width: 100%;">
                                    <option value="">Pilih Status Karyawan</option>
                                    <option value="AT">AT</option>
                                    <option value="MD">MD</option>
                                    <option value="HK">HK</option>
                                    <option value="PS">PS</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterJeniskontrak">Jenis Perjanjian Kerja</label>
                                <select name="filterJeniskontrak" id="filterJeniskontrak" class="form-control"
                                    style="width: 100%;">
                                    <option value="">Pilih Jenis Kontrak</option>
                                    <option value="PKWT">PKWT</option>
                                    <option value="PKWTT">PKWTT</option>
                                    <option value="MAGANG">MAGANG</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="form-group">
                                <label for="filterJeniskelamin">Jenis Kelamin</label>
                                <select name="filterJeniskelamin" id="filterJeniskelamin" class="form-control"
                                    style="width: 100%;">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L">LAKI-LAKI</option>
                                    <option value="P">PEREMPUAN</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterAgama">Agama</label>
                                <select name="filterAgama" id="filterAgama" class="form-control" style="width: 100%;">
                                    <option value="">Pilih Agama (Boleh Kosong)</option>
                                    <option value="ISLAM">ISLAM</option>
                                    <option value="KRISTEN">KRISTEN</option>
                                    <option value="PROTESTAN">PROTESTAN</option>
                                    <option value="KONGHUCU">KONGHUCU</option>
                                    <option value="HINDU">HINDU</option>
                                    <option value="BUDHA">BUDHA</option>
                                    <option value="LAINNYA">LAINNYA</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filterStatuskeluarga">Status Keluarga</label>
                                <select name="filterStatuskeluarga" id="filterStatuskeluarga" class="form-control"
                                    style="width: 100%;">
                                    <option value="">Pilih Status Keluarga</option>
                                    <option value="MENIKAH">MENIKAH</option>
                                    <option value="BELUM MENIKAH">BELUM MENIKAH</option>
                                    <option value="CERAI">CERAI</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Kategori Keluarga</label>
                                <select name="filterKategorikeluarga" id="filterKategorikeluarga"
                                    class="form-control" style="width: 100%;">
                                    <option value="">Pilih Kategori Keluarga</option>
                                    <option value="TK0">TK0</option>
                                    <option value="TK1">TK1</option>
                                    <option value="TK2">TK2</option>
                                    <option value="TK3">TK3</option>
                                    <option value="K0">K0</option>
                                    <option value="K1">K1</option>
                                    <option value="K2">K2</option>
                                    <option value="K3">K3</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Rekening Bank</label>
                                <select name="filterNamabank" id="filterNamabank" class="form-control"
                                    style="width: 100%;">
                                    <option value="">Pilih Bank Rekening</option>
                                    <option value="MANDIRI">MANDIRI</option>
                                    <option value="BRI">BRI</option>
                                    <option value="BNI">BNI</option>
                                    <option value="BCA">BCA</option>
                                    <option value="BSI">BSI</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Golongan Darah</label>
                                <select name="filterGolongandarah" id="filterGolongandarah" class="form-control"
                                    style="width: 100%;">
                                    <option value="">Pilih Golongan Darah</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-12 d-flex justify-content-end gap-1">
                        <button type="button" class="waves-effect waves-light btn btn-danger btnResetFilter"><i
                                class="fas fa-history"></i> Reset</button>
                        <button type="button" class="waves-effect waves-light btn btn-warning btnSubmitFilter"><i
                                class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</div>
<!-- /.modal -->
