<a href="{{ route('izine.pengajuan-izin') }}">
    <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
    <span>Pengajuan Izin</span>
    @if ($izine['pengajuan_izin'] > 0)
        <span class="pull-right-container" style="right:10px!important; top:55%!important; margin-top:-13px!important;">
            <div class="badge bg-danger m-0" style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                {{ $izine['pengajuan_izin'] }}
            </div>
        </span>
    @endif
</a>
