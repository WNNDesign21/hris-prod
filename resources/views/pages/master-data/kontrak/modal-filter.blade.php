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
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">No Surat/Perjanjian</label>
                                <input type="text" name="filterNosurat" id="filterNosurat" class="form-control"
                                    placeholder="Masukkan No Surat atau Perjanjian" style="width: 100%;">
                            </div>
                            <div class="form-group">
                                <label for="">Nama</label>
                                <input type="text" name="filterNama" id="filterNama" class="form-control"
                                    placeholder="Masukkan Nama Karyawan" style="width: 100%;">
                            </div>
                            <div class="form-group">
                                <label for="">Nama Posisi</label>
                                <input type="text" name="filterNamaposisi" id="filterNamaposisi" class="form-control"
                                    placeholder="Masukkan Nama Posisi" style="width: 100%;">
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label for="">Tanggal Mulai (Start)</label>
                                        <input type="date" name="filterTanggalmulaistart"
                                            id="filterTanggalmulaistart" class="form-control" style="width: 100%;">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label for="">Tanggal Mulai (End)</label>
                                        <input type="date" name="filterTanggalmulaiend" id="filterTanggalmulaiend"
                                            class="form-control" style="width: 100%;">
                                    </div>
                                </div>
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
                                <label for="filterJeniskontrak">Jenis Kontrak</label>
                                <select name="filterJeniskontrak" id="filterJeniskontrak" class="form-control"
                                    style="width: 100%;">
                                    <option value="">Pilih Jenis Kontrak</option>
                                    <option value="PKWT">PKWT</option>
                                    <option value="PKWTT">PKWTT</option>
                                    <option value="MAGANG">MAGANG</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label for="filterAttachment">Attachment</label>
                                        <select name="filterAttachment" id="filterAttachment" class="form-control"
                                            style="width: 100%;">
                                            <option value="">Pilih Attachment</option>
                                            <option value="Y">YES</option>
                                            <option value="N">NO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label for="filterEvidence">Evidence</label>
                                        <select name="filterEvidence" id="filterEvidence" class="form-control"
                                            style="width: 100%;">
                                            <option value="">Pilih Evidence</option>
                                            <option value="Y">YES</option>
                                            <option value="N">NO</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="filterStatuskontrak">Status Kontrak</label>
                                <select name="filterStatuskontrak" id="filterStatuskontrak" class="form-control"
                                    style="width: 100%;">
                                    <option value="">Pilih Status</option>
                                    <option value="DONE">DONE</option>
                                    <option value="ON PROGRESS">ON PROGRESS</option>
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
