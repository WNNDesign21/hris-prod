<a href="{{ route('tugasluare.approval') }}">
    <i class="icon-Double-check"><span class="path1"></span><span class="path2"></span></i>
    <span>Approval TL</span>
    @if ($tugasluare['approval'] > 0)
        <span class="pull-right-container" style="right:10px!important; top:55%!important; margin-top:-13px!important;">
            <div class="badge bg-danger m-0" style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                {{ $tugasluare['approval'] }}
            </div>
        </span>
    @endif
</a>
