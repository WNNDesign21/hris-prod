<div class="col-6 col-md-4 col-xl-3">
    <a class="box box-link-shadow text-center btnDetailSummary" href="javascript:void(0)" style="cursor: pointer;"
        data-type="1">
        <div class="box-body py-25 bg-primary bbsr-0 bber-0">
            <p class="fw-600 fs-24">HADIR</p>
        </div>
        <div class="box-body">
            <h3><span class="text-primary fs-40 hadirText">{{ $hadir . '/' . $total_karyawan }}</span></h3>
        </div>
    </a>
</div>
<div class="col-6 col-md-4 col-xl-3">
    <a class="box box-link-shadow text-center btnDetailSummary" href="javascript:void(0)" style="cursor: pointer;"
        data-type="2">
        <div class="box-body py-25 bg-danger bbsr-0 bber-0">
            <p class="fw-600 fs-24">SAKIT</p>
        </div>
        <div class="box-body">
            <h3><span class="text-danger fs-40 sakitText">{{ $sakit . '/' . $total_karyawan }}</span></h3>
        </div>
    </a>
</div>
<div class="col-6 col-md-4 col-xl-3">
    <a class="box box-link-shadow text-center btnDetailSummary" href="javascript:void(0)" style="cursor: pointer;"
        data-type="3">
        <div class="box-body py-25 bg-info bbsr-0 bber-0">
            <p class="fw-600 fs-24">IZIN</p>
        </div>
        <div class="box-body">
            <h3><span class="text-info fs-40 izinText">{{ $izin . '/' . $total_karyawan }}</span></h3>
        </div>
    </a>
</div>
<div class="col-6 col-md-4 col-xl-3">
    <a class="box box-link-shadow text-center btnDetailSummary" href="javascript:void(0)" style="cursor: pointer;"
        data-type="4">
        <div class="box-body py-25 bg-success bbsr-0 bber-0">
            <p class="fw-600 fs-24">CUTI</p>
        </div>
        <div class="box-body">
            <h3><span class="text-success fs-40 cutiText">{{ $cuti . '/' . $total_karyawan }}</span></h3>
        </div>
    </a>
</div>
