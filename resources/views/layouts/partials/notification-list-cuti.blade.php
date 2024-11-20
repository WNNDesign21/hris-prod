<a href="{{ route('cutie.personalia-cuti') }}">
    <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
    <span>List Cuti</span>
    @if ($notification['count_cutie_approval'] > 0)
        <span class="pull-right-container" style="right:10px!important; top:55%!important; margin-top:-13px!important;">
            <div class="badge bg-danger m-0" style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                {{ $notification['count_cutie_approval'] }}
            </div>
        </span>
    @endif
</a>
