<!-- modal Area -->
<div class="modal fade" id="modal-kontrak">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title p-4" id="title-kontrak"></h4>
                <button type="button" class="btn-close btnCloseKontrak" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h4 class="box-title ">Form Kontrak</h4>
                                    <ul class="box-controls pull-right">
                                        <li><a class="box-btn-slide" href="#"></a></li>
                                    </ul>
                                </div>

                                <div class="box-body">
                                    <form action="#" method="POST" enctype="multipart/form-data" id="form-kontrak">
                                        @csrf
                                        <div class="row p-4">
                                            <div class="col-lg-6 col-12">
                                                <div class="form-group">
                                                    <input type="hidden" name="karyawan_id_kontrakEdit"
                                                        id="karyawan_id_kontrakEdit">
                                                    <label for="">Jenis Kontrak <span
                                                            class="text-danger">*</span></label>
                                                    <select name="jenis_kontrakEdit" id="jenis_kontrakEdit"
                                                        class="form-control select2" style="width: 100%;">
                                                        <option value="PKWT">PKWT</option>
                                                        <option value="PKWTT">PKWTT</option>
                                                        <option value="MAGANG">MAGANG</option>
                                                        <option value="THL">THL</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Posisi <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" id="posisi_kontrakEdit"
                                                        name="posisi_kontrakEdit" style="width: 100%;">
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Durasi (Dalam Bulan)</label>
                                                    <input type="number" name="durasi_kontrakEdit"
                                                        id="durasi_kontrakEdit" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-12">
                                                <div class="form-group">
                                                    <label for="">Salary</label>
                                                    <input type="text" name="salary_kontrakEdit"
                                                        id="salary_kontrakEdit" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Tanggal Mulai</label>
                                                    <input type="date" name="tanggal_mulaiEdit"
                                                        id="tanggal_mulaiEdit" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Deskripsi</label>
                                                    <textarea name="deskripsi_kontrakEdit" id="deskripsi_kontrakEdit" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end mt-2">
                                                <button type="submit" class="btn btn-success"><i
                                                        class="fas fa-save"></i>
                                                    Save</button>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="panel-group panel-group-simple panel-group-continuous mb-2"
                                    id="list-kontrak" aria-multiselectable="true" role="tablist">
                                    <div class="panel p-4">
                                        <div class="panel-heading" id="kontrak-1" role="tab">
                                            <a class="panel-title" aria-controls="kontrak-content-1"
                                                aria-expanded="true" data-bs-toggle="collapse" href="#kontrak-content-1"
                                                data-parent="#list-kontrak">
                                                <h5>KONTRAK-2445023012</h5>
                                            </a>
                                        </div>
                                        <div class="panel-collapse collapse mt-2" id="kontrak-content-1"
                                            aria-labelledby="kontrak-1" role="tabpanel" data-bs-parent="#category-1">
                                            <div class="panel-body">
                                                <div class="row mt-5">
                                                    <div class="col-lg-6 col-12">
                                                        <div class="form-group">
                                                            <label for="" class="fw-light">Jenis
                                                                Kontrak</label>
                                                            <h5>PKWT</h5>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="" class="fw-light">Posisi</label>
                                                            <h5>PROGRAMMER</h5>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="" class="fw-light">Status</label>
                                                            <h5>WAITING</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-12">
                                                        <div class="form-group">
                                                            <label for="" class="fw-light">Periode
                                                                Kontrak</label>
                                                            <h5>25 Januari 2025 - 25 Desember 2025</h5>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="" class="fw-light">Salary</label>
                                                            <h5>Rp 5.600.000</h5>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="" class="fw-light">Deskripsi</label>
                                                            <h5>Deskripsi awkdmawdaiwwdawodkaowdkawodw</h5>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="" class="fw-light">Soft Copy</label>
                                                            <h5><a href=""><i class="fas fa-download"></i>
                                                                    Unduh
                                                                    Dokumen Disini</a></h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
    <!-- /.modal -->
