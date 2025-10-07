<!-- modal Area -->
<div class="modal fade" id="modal-input">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buat KSK</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('ksk.release.store') }}" method="POST" enctype="multipart/form-data"
                    id="form-input">
                    @csrf
                    <div class="form-group">
                        <div class="col-12">
                            <div class="box">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-center text-center mb-3">
                                            <h1 class="box-title">KONFIRMASI STATUS KARYAWAN</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <input type="hidden" name="id_divisi_header" id="id_divisi_header">
                                        <input type="hidden" name="nama_divisi_header" id="nama_divisi_header">
                                        <input type="hidden" name="id_departemen_header" id="id_departemen_header">
                                        <input type="hidden" name="nama_departemen_header" id="nama_departemen_header">
                                        <input type="hidden" name="parent_id_header" id="parent_id_header">
                                        <input type="hidden" name="tahun_selesai_header" id="tahun_selesai_header">
                                        <input type="hidden" name="bulan_selesai_header" id="bulan_selesai_header">
                                        <p>Divisi : <span id="divisi">ADMINISTRASI</span></p>
                                        <p>Departemen : <span id="departemen">ICT</span></p>
                                        <p>Release Date : <span id="release_date">Senin, 25 Maret 2025</span></p>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row" id="list-karyawan">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary waves-effect">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
