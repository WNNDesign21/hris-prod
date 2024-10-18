<!-- modal Area -->
<div class="modal fade" id="modal-profile">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title p-4">Profile</h4>
                <button type="button" class="btn-close btnCloseProfile" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row p-4">
                        <div class="col-lg-6 col-12">
                            <div class="form-group">
                                <a id="link_foto_profile" href="{{ $profile['foto'] }}" data-title="Foto Karyawan"
                                    class="image-popup-vertical-fit">
                                    <img id="image_review_profile" src="{{ $profile['foto'] }}" alt="Foto Karyawan"
                                        style="width: 150px;height: 150px;" class="img-thumbnail img-fluid">
                                </a>
                            </div>
                            <div class="form-group">
                                <label for="">Nomor Induk Karyawan <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="ni_karyawan" id="ni_karyawan" class="form-control"
                                        value="{{ $profile['ni_karyawan'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Nama <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="hidden" name="id_karyawan" id="id_karyawan" class="form-control"
                                        disabled>
                                    <input type="text" name="nama" id="nama" class="form-control"
                                        value="{{ $profile['nama'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">No. KK <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="no_kk" id="no_kk" class="form-control"
                                        value="{{ $profile['no_kk'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">NIK <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="nik" id="nik" class="form-control"
                                        value="{{ $profile['nik'] }}" disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Kota Lahir <span class="text-danger">*</span></label>
                                        <br>
                                        <div class="input-group mb-2" style="width:100%;">
                                            <input type="text" name="tempat_lahir" id="tempat_lahir"
                                                class="form-control" value="{{ $profile['tempat_lahir'] }}" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <br>
                                        <div class="input-group mb-2" style="width:100%;">
                                            <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                                class="form-control" value="{{ $profile['tanggal_lahir'] }}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Jenis Kelamin <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control"
                                        style="width: 100%;" disabled>
                                        <option value="L"
                                            {{ $profile['jenis_kelamin'] == 'L' ? 'selected' : '' }}>
                                            Laki-laki</option>
                                        <option value="P"
                                            {{ $profile['jenis_kelamin'] == 'P' ? 'selected' : '' }}>
                                            Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Agama <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <div class="input-group mb-2" style="width:100%;">
                                        <input type="text" class="form-control" id="agama" name="agama"
                                            value="{{ $profile['agama'] }}" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Gol. Darah <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" class="form-control" id="gol_darah" name="gol_darah"
                                        value="{{ $profile['gol_darah'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Status Keluarga <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" class="form-control" id="status_keluarga"
                                        name="status_keluarga" value="{{ $profile['status_keluarga'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Kategori Keluarga <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" class="form-control" id="kategori_keluarga"
                                        name="kategori_keluarga" value="{{ $profile['kategori_keluarga'] }}"
                                        disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Alamat KTP <span class="text-danger">*</span></label>
                                <br>
                                <textarea name="alamat" id="alamat" class="form-control" disabled>{{ $profile['alamat'] }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="">Domisili <span class="text-danger">*</span></label>
                                <br>
                                <textarea name="domisili" id="domisili" class="form-control" disabled>{{ $profile['domisili'] }}</textarea>
                            </div>
                            <div class="form-group mt-2">
                                <label for="">No. Rekening <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="no_rekening" id="no_rekening" class="form-control"
                                        value="{{ $profile['no_rekening'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label for="">Atas Nama Rekening <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="nama_rekening" id="nama_rekening"
                                        class="form-control" value="{{ $profile['nama_rekening'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Nama Bank <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="nama_bank" id="nama_bank" class="form-control"
                                        value="{{ $profile['nama_bank'] }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="form-group mt-2">
                                <label for="">Nama Ibu Kandung <span class="text-danger">*</span></label>
                                <br>
                                <input type="text" name="nama_ibu_kandung" id="nama_ibu_kandung"
                                    class="form-control" value="{{ $profile['nama_ibu_kandung'] }}" disabled>
                            </div>
                            <div class="form-group">
                                <label for="">Jenjang Pendidikan Terakhir <span
                                        class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="jenjang_pendidikan" id="jenjang_pendidikan"
                                        class="form-control" value="{{ $profile['jenjang_pendidikan'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label for="">Jurusan Pendidikan Terakhir <span
                                        class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="jurusan_pendidikan" id="jurusan_pendidikan"
                                        class="form-control" value="{{ $profile['jurusan_pendidikan'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">No. Telepon <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="no_telp" id="no_telp" class="form-control"
                                        value="{{ $profile['no_telp'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label for="">No.Telp Darurat <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="no_telp_darurat" id="no_telp_darurat"
                                        class="form-control" value="{{ $profile['no_telp_darurat'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label for="">Email</label>
                                <br>
                                <small>Note : Email ini untuk keperluan notifikasi sistem</small>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="{{ $profile['email'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label for="">NPWP <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="npwp" id="npwp" class="form-control"
                                        value="{{ $profile['npwp'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label for="">No. BPJS Kesehatan <span class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="no_bpjs_ks" id="no_bpjs_ks" class="form-control"
                                        value="{{ $profile['no_bpjs_ks'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label for="">No. BPJS Ketenagakerjaan <span
                                        class="text-danger">*</span></label>
                                <br>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="no_bpjs_kt" id="no_bpjs_kt" class="form-control"
                                        value="{{ $profile['no_bpjs_kt'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label for="">Tanggal Mulai <span class="text-danger">*</span></label>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                        class="form-control" value="{{ $profile['tanggal_mulai'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label for="">Tanggal Selesai <span class="text-danger">*</span></label>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                        class="form-control" value="{{ $profile['tanggal_selesai'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status Karyawan <span class="text-danger">*</span></label>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="status_karyawan" id="status_karyawan"
                                        class="form-control" value="{{ $profile['status_karyawan'] }}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Posisi <span class="text-danger">*</span></label>
                                <div class="media-list media-list-hover media-list-divided">
                                    @if ($profile['posisi'] !== null)
                                        @foreach ($profile['posisi'] as $posisi)
                                            <a class="media media-single" href="javascript:void(0);">
                                                <i class="fs-18 fa fa-user"></i>
                                                <span class="title">{{ $posisi }} </span>
                                            </a>
                                        @endforeach
                                    @else
                                        <a class="media media-single" href="javascript:void(0);">
                                            <i class="fs-18 fa fa-user"></i>
                                            <span class="title">Belum Ada Posisi</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Grup <span class="text-danger">*</span></label>
                                <div class="input-group mb-2" style="width:100%;">
                                    <input type="text" name="grup" id="grup" class="form-control"
                                        value="{{ $profile['grup'] }}" disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label class="form-label">Jatah Cuti Pribadi</label>
                                        <div class="input-group mb-2" style="width:100%;">
                                            <input type="text" name="sisa_cuti_pribadi" id="sisa_cuti_pribadi"
                                                class="form-control" value="{{ $profile['sisa_cuti_pribadi'] }}"
                                                disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label class="form-label">Jatah Cuti Bersama</label>
                                        <div class="input-group mb-2" style="width:100%;">
                                            <input type="text" name="sisa_cuti_bersama" id="sisa_cuti_bersama"
                                                class="form-control" value="{{ $profile['sisa_cuti_bersama'] }}"
                                                disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label class="form-label">Jatah Cuti Tahun Lalu</label>
                                        <input type="number" name="sisa_cuti_tahun_lalu" id="sisa_cuti_tahun_lalu"
                                            class="form-control" min='0' max='12'
                                            value="{{ $profile['sisa_cuti_tahun_lalu'] }}" disabled>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label class="form-label">Expired Date Cuti Tahun Lalu</label>
                                        <input type="date" name="expired_date_cuti_tahun_lalu"
                                            id="expired_date_cuti_tahun_lalu"
                                            value="{{ $profile['expired_date_cuti_tahun_lalu'] }}"
                                            class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label class="form-label">Hutang Cuti</label>
                                        <div class="input-group mb-2" style="width:100%;">
                                            <input type="text" name="hutang_cuti" id="hutang_cuti"
                                                class="form-control" value="{{ $profile['hutang_cuti'] }}" disabled>
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
