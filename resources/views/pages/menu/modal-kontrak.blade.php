<div class="modal fade" id="modal-kontrak">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title p-4">My Contract</h4>
                <button type="button" class="btn-close btnCloseKontrak" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12">
                            <div class="col-12">
                                <div class="panel-group panel-group-simple panel-group-continuous mb-2"
                                    id="list-kontrak" aria-multiselectable="true" role="tablist">
                                    @if (!empty($kontrak))
                                        @foreach ($kontrak as $idx => $val)
                                            <div class="panel p-4 mb-3">
                                                <div class="panel-heading" id="kontrak-{{ $idx }}"
                                                    role="tab">
                                                    <a class="panel-title"
                                                        aria-controls="kontrak-content-{{ $idx }}"
                                                        aria-expanded="true" data-bs-toggle="collapse"
                                                        href="#kontrak-content-{{ $idx }}"
                                                        data-parent="#list-kontrak">
                                                        <div class="row d-flex justify-content-between">
                                                            <div class="col flex-col">
                                                                <small>{{ $val['no_surat'] }}</small>
                                                                <h5>{{ $val['id_kontrak'] }}</h5>
                                                                <small class="mt-0">{{ $val['tempat_administrasi'] }},
                                                                    {{ $val['issued_date_text'] }}</small>
                                                            </div>
                                                            <div class="col text-end">
                                                                <h5>{!! $val['status_badge'] !!}</h5>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="panel-collapse collapse mt-2"
                                                    id="kontrak-content-{{ $idx }}"
                                                    aria-labelledby="kontrak-{{ $idx }}" role="tabpanel"
                                                    data-bs-parent="#kontrak-{{ $idx }}">
                                                    <div class="panel-body">
                                                        <div class="row mt-5">
                                                            <div class="col-lg-6 col-12">
                                                                <div class="form-group">
                                                                    <label for="" class="fw-light">Jenis
                                                                        Kontrak</label>
                                                                    <h5>{{ $val['jenis'] }}</h5>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for=""
                                                                        class="fw-light">Posisi</label>
                                                                    <h5>{{ $val['nama_posisi'] }}</h5>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for=""
                                                                        class="fw-light">Status</label>
                                                                    <h5>{{ $val['status'] }}</h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-12">
                                                                <div class="form-group">
                                                                    <label for="" class="fw-light">Periode
                                                                        Kontrak</label>
                                                                    <h5>{{ $val['tanggal_mulai'] }} -
                                                                        {{ $val['tanggal_selesai'] }}</h5>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for=""
                                                                        class="fw-light">Salary</label>
                                                                    <h5>{{ $val['salary'] }}</h5>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for=""
                                                                        class="fw-light">Deskripsi</label>
                                                                    <h5>{{ $val['deskripsi'] }}</h5>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="" class="fw-light">Template
                                                                        Print</label>
                                                                    <h5><a href="{{ url('/master-data/kontrak/download-kontrak-kerja/' . $val['id_kontrak']) }}"
                                                                            target="_blank"><i
                                                                                class="fas fa-download"></i>
                                                                            Unduh
                                                                            Template Disini</a></h5>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="" class="fw-light">Dokumen
                                                                        Asli</label>
                                                                    <h5>{!! $val['attachment']
                                                                        ? '<a href="' . $val['attachment'] . '" target="_blank"><i class="fas fa-download"></i> Unduh Dokumen Disini</a>'
                                                                        : '-' !!}</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
