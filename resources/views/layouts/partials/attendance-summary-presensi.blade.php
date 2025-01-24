<div class="col-12 col-lg-3">
    <div class="box-body be-1 border-light">
        <div class="flexbox mb-1">
            <span>
                <span class="icon-User fs-40"><span class="path1"></span><span class="path2"></span></span><br>
                HADIR
            </span>
            <span class="text-primary fs-40 hadirText">{{ $hadir . '/' . $total_karyawan }}</span>
        </div>
        <div class="progress progress-xxs mt-10 mb-0">
            <div class="progress-bar" role="progressbar"
                style="width: {{ ($hadir / $total_karyawan) * 100 }}%; height: 4px;"
                aria-valuenow="{{ ($hadir / $total_karyawan) * 100 }}" aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>
    </div>
</div>
<div class="col-12 col-lg-3 hidden-down">
    <div class="box-body be-1 border-light">
        <div class="flexbox mb-1">
            <span>
                <span class="icon-Bed fs-40"><span class="path1"></span><span class="path2"></span></span><br>
                SAKIT
            </span>
            <span class="text-info fs-40 sakitText">{{ $sakit . '/' . $total_karyawan }}</span>
        </div>
        <div class="progress progress-xxs mt-10 mb-0">
            <div class="progress-bar bg-info" role="progressbar"
                style="width: {{ ($sakit / $total_karyawan) * 100 }}%; height: 4px;"
                aria-valuenow="{{ ($sakit / $total_karyawan) * 100 }}" aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>
    </div>
</div>
<div class="col-12 col-lg-3 d-none d-lg-block">
    <div class="box-body be-1 border-light">
        <div class="flexbox mb-1">
            <span>
                <span class="icon-Book fs-40"><span class="path1"></span><span class="path2"></span><span
                        class="path3"></span></span><br>
                IZIN
            </span>
            <span class="text-warning fs-40 izinText">{{ $izin . '/' . $total_karyawan }}</span>
        </div>
        <div class="progress progress-xxs mt-10 mb-0">
            <div class="progress-bar bg-warning" role="progressbar"
                style="width: {{ ($izin / $total_karyawan) * 100 }}%; height: 4px;"
                aria-valuenow="{{ ($izin / $total_karyawan) * 100 }}" aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>
    </div>
</div>
<div class="col-12 col-lg-3 d-none d-lg-block">
    <div class="box-body">
        <div class="flexbox mb-1">
            <span>
                <span class="icon-Direction fs-40"><span class="path1"></span><span class="path2"></span></span><br>
                CUTI
            </span>
            <span class="text-danger fs-40 cutiText">{{ $cuti . '/' . $total_karyawan }}</span>
        </div>
        <div class="progress progress-xxs mt-10 mb-0">
            <div class="progress-bar bg-danger" role="progressbar"
                style="width: {{ ($cuti / $total_karyawan) * 100 }}%; height: 4px;"
                aria-valuenow="{{ ($cuti / $total_karyawan) * 100 }}" aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>
    </div>
</div>
