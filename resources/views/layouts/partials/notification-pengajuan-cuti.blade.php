<a href="{{ route('cutie.pengajuan-cuti.index') }}">
    <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
    <span>Pengajuan Cuti</span>
    @if ($notification['count_my_cutie'] + $notification['count_rejected_cuti'] > 0)
        <span class="pull-right-container" style="right:10px!important; top:55%!important; margin-top:-13px!important;">
            <div class="badge bg-danger m-0" style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                {{ $notification['count_my_cutie'] + $notification['count_rejected_cuti'] }}
            </div>
        </span>
    @endif
</a>
