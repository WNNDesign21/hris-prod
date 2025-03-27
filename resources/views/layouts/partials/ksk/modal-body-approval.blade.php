<div class="form-group">
    <div class="col-12">
        <div class="box p-4">
            <div class="box-body">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center text-center mb-3">
                        <h1 class="box-title">KONFIRMASI STATUS KARYAWAN</h4>
                    </div>
                </div>
                <div class="row">
                    <p>Divisi : {{ $ksk->nama_divisi }}</p>
                    <p>Departemen : {{ $ksk->nama_departemen }}</p>
                    <p>Release Date : {{ $ksk->releaseDate }}</p>
                </div>
                <div class="row">
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <small class="text-muted">Leader</small><br>
                            @if ($ksk->released_by)
                                <p>{{ '✅' . $ksk->released_by . '<br>' . \Carbon\Carbon::createFromFormat($ksk->released_at)->format('d F Y H:i') }}
                                </p>
                            @else
                                <p>⏳ Waiting</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <small class="text-muted">Section Head</small><br>
                            @if ($ksk->checked_by)
                                <p>{{ '✅' . $ksk->checked_by . '<br>' . \Carbon\Carbon::createFromFormat($ksk->checked_at)->format('d F Y H:i') }}
                                </p>
                            @else
                                <p>⏳ Waiting</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <small class="text-muted">Dept.Head</small><br>
                            @if ($ksk->approved_by)
                                <p>{{ '✅' . $ksk->approved_by . '<br>' . \Carbon\Carbon::createFromFormat($ksk->approved_at)->format('d F Y H:i') }}
                                </p>
                            @else
                                <p>⏳ Waiting</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <small class="text-muted">Div.Head</small><br>
                            @if ($ksk->reviewed_div_by)
                                <p>{{ '✅' . $ksk->reviewed_div_by . '<br>' . \Carbon\Carbon::createFromFormat($ksk->reviewed_div_at)->format('d F Y H:i') }}
                                </p>
                            @else
                                <p>⏳ Waiting</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <small class="text-muted">Plant Head</small><br>
                            @if ($ksk->reviewed_ph_by)
                                <p>{{ '✅' . $ksk->reviewed_ph_by . '<br>' . \Carbon\Carbon::createFromFormat($ksk->reviewed_ph_at)->format('d F Y H:i') }}
                                </p>
                            @else
                                <p>⏳ Waiting</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="form-group">
                            <small class="text-muted">Director</small><br>
                            @if ($ksk->reviewed_dir_by)
                                <p>{{ '✅' . $ksk->reviewed_dir_by . '<br>' . \Carbon\Carbon::createFromFormat($ksk->reviewed_dir_at)->format('d F Y H:i') }}
                                </p>
                            @else
                                <p>⏳ Waiting</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="panel-group panel-group-simple panel-group-continuous mb-2" id="list-approval-ksk"
                    aria-multiselectable="true" role="tablist">
                    @include('layouts.partials.ksk.ksk-list-karyawan-approval', [
                        'datas' => $ksk->detailKSK,
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
