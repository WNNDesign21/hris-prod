<!-- modal Area -->
<div class="modal fade" id="modal-lapor-skd">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Lapor SKD</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('izine.lapor-skd.store') }}" method="POST" enctype="multipart/form-data"
                        id="form-lapor-skd">
                        @csrf
                        <div class="row p-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <small class="text-fade">Note <span class="text-danger">*</span> : Jika SKD belum
                                        ada, silahkan isi
                                        tanggal mulai dan kosongkan tanggal selesai (Bisa di upload menyusul) lalu beri
                                        keterangan, namun jika SKD sudah ada, maka upload file SKD pada Lampiran dan isi
                                        semua field (Field Keterangan menjadi opsional ketika SKD sudah ada)</small>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_mulai" id="label_tanggal_mulai">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_selesai" id="label_tanggal_selesai">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                        class="form-control">
                                </div>
                                <a id="linkFoto" href="{{ asset('img/no-image.png') }}"
                                    class="image-popup-vertical-fit" data-title="Lampiran SKD">
                                    <img id="imageReview" src="{{ asset('img/no-image.png') }}" alt="Image Foto"
                                        style="width: 150px;height: 150px;" class="img-fluid">
                                </a>
                                <div class="form-group">
                                    <label for="">Lampiran SKD</label>
                                    <br>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary" id="btnUploadLampiranSkd"><i
                                                class="fas fa-upload"></i> Upload</button>
                                        <button type="button" class="btn btn-danger" id="btnResetLampiranSkd"><i
                                                class="fas fa-trash"></i> Reset</button>
                                    </div>
                                    <input type="file" name="lampiran_skd" id="lampiran_skd" class="form-control"
                                        style="display: none;">
                                </div>
                                <div class="form-group">
                                    <small>Note : Lampiran bisa di upload menyusul</small>
                                </div>
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <textarea class="form-control" name="keterangan" id="keterangan" placeholder="Tulis keterangan sakit jika diperlukan..."
                                        style="width: 100%;"></textarea>
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
