<div class="modal fade" id="modal-new-scanlog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Data Presensi Baru Ditemukan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Berikut adalah data presensi terbaru yang berhasil ditarik dari mesin. Tabel akan dimuat ulang setelah Anda menutup jendela ini.</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nama Karyawan</th>
                                <th>Waktu Scan</th>
                            </tr>
                        </thead>
                        <tbody id="new-scanlog-content">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup dan Muat Ulang Tabel</button>
            </div>
        </div>
    </div>
</div>