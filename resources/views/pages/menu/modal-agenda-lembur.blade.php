<div class="modal fade" id="modal-agenda-lembur">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title p-4">Agenda Lembur</h4>
                <button type="button" class="btn-close btnCloseAgendaLembur" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        @if (!empty($agendaLembur))
                            @foreach ($agendaLembur as $item)
                                <div class="col-12 px-4">
                                    <div class="panel-group panel-group-simple panel-group-continuous mb-2"
                                        id="list-agenda-lembur" aria-multiselectable="true" role="tablist">
                                        <div class="box">
                                            <div class="box-header d-flex justify-content-between">
                                                <h4 class="box-title" style="width: 60%;"><span
                                                        class="text-success">{{ $item['lembur_id'] }}</span></h4>
                                                <div>{!! $item['status'] !!}</div>
                                            </div>
                                            <div class="box-body p-0">
                                                <div class="media-list media-list">
                                                    <div class="media bar-0">
                                                        <div class="media-body">
                                                            <p>Deskripsi Pekerjaan : <br>
                                                                <span
                                                                    class="text-primary">{{ $item['deskripsi_pekerjaan'] }}</span>
                                                            </p>
                                                            <br>
                                                            <p>Rencana Mulai : <br>
                                                                <span
                                                                    class="text-primary">{{ $item['rencana_mulai_lembur'] }}
                                                                    WIB</span>
                                                            </p>
                                                            <br>
                                                            <p>Rencana Selesai : <br>
                                                                <span
                                                                    class="text-primary">{{ $item['rencana_selesai_lembur'] }}
                                                                    WIB</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <h4 class="text-center p-4">Tidak ada agenda lembur.</h4>
                        @endif

                        {{-- <div class="col-12 p-4">
                            <div class="panel-group panel-group-simple panel-group-continuous mb-2"
                                id="list-agenda-lembur" aria-multiselectable="true" role="tablist">
                                <div class="box">
                                    <div class="box-header d-flex justify-content-between">
                                        <h4 class="box-title"><span class="text-success">Membuat Pak Roni
                                                Senang</span></h4>
                                        <span class="badge badge-warning">WAITING</span>
                                    </div>
                                    <div class="box-body p-0">
                                        <div class="media-list media-list">
                                            <div class="media bar-0">
                                                <div class="media-body">
                                                    <p class="mb-0">
                                                        <a class="hover-success fs-16" href="#"></a>
                                                    </p>
                                                    <h6>Rencana Mulai : <span class="text-primary">26 November 2024
                                                            22:00 WIB</span>
                                                    </h6>
                                                    <h6>Rencana Selesai : <span class="text-primary">27
                                                            November 2024 07:00 WIB</span></h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
