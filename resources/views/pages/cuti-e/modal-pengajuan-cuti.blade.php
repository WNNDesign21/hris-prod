<!-- modal Area -->
<div class="modal fade" id="modal-pengajuan-cuti">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Pengajuan Cuti</h4>
                <button type="button" class="btn-close btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="{{ route('cutie.pengajuan-cuti.store') }}" method="POST"
                        enctype="multipart/form-data" id="form-pengajuan-cuti">
                        @csrf
                        <div class="row p-4">
                            <div class="col-12">
                                <input type="hidden" id="id_cuti" name="id_cuti">
                                <input type="hidden" id="durasi_cuti" name="durasi_cuti">
                                <div class="form-group">
                                    <label for="">Jenis Cuti<span class="text-danger">*</span></label>
                                    {{-- <br> --}}
                                    {{-- <small>Note : Jatah cuti pribadi setiap karyawan maksimal 12 hari kerja (Reset
                                        mengikuti tanggal bergabung pada kontrak) <br> Cuti bersama
                                        diambil dari jatah cuti pribadi sebanyak 6 Hari (Lebaran & Natal) <br> Jika
                                        masa kerja karyawan masih < 12 bulan (Belum memiliki hak cuti), maka cuti
                                            bersama akan menjadi Hutang Cuti sehingga jatah cuti tahun depan (12) jika
                                            perpanjang kontrak akan otomatis terpotong jika memiliki hutang cuti
                                            sedangkan bagi karyawan yang tidak melanjutkan kontrak namun masih memiliki
                                            hutang cuti, maka akan terjadi pemotongan gaji pada gaji kontrak terakhir,
                                            maka dari itu setiap karyawan hanya memiliki jatah cuti pribadi (selain cuti
                                            bersama) maksimal 6 Hari dan akan hangus jika tidak dipakai (cuti bersama
                                            tidak perlu diinput)</small> --}}
                                    <select name="jenis_cuti" id="jenis_cuti" class="form-control" style="width: 100%;">
                                        <option value="PRIBADI">PRIBADI</option>
                                        <option value="KHUSUS">KHUSUS</option>
                                    </select>
                                </div>
                                <div class="form-group" id="conditional_field">
                                </div>
                                <div class="form-group">
                                    <label for="">Rencana Mulai</label>
                                    <input type="date" name="rencana_mulai_cuti" id="rencana_mulai_cuti"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Rencana Selesai</label>
                                    <br>
                                    <small>Note : Jika rencana selesai jatuh pada hari sabtu/minggu, maka
                                        sisa durasi
                                        tersebut otomatis akan dialihkan pada hari kerja berikutnya (Cuti Khusus)
                                    </small>
                                    <input type="date" name="rencana_selesai_cuti" id="rencana_selesai_cuti"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Alasan Cuti</label>
                                    <textarea class="form-control" name="alasan_cuti" id="alasan_cuti"
                                        placeholder="Jika Cuti Pribadi, Wajib diisi untuk pertimbangan atasan!" style="width: 100%;"></textarea>
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
