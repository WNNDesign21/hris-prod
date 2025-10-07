<a href="{{ route('izine.approval-skd') }}">
    <i class="icon-Double-check"><span class="path1"></span><span class="path2"></span></i>
    <span>Approval SKD</span>
    @if ($izine['approval_skd'] > 0)
        <span class="pull-right-container" style="right:10px!important; top:55%!important; margin-top:-13px!important;">
            <div class="badge bg-danger m-0" style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                {{ $izine['approval_skd'] }}
            </div>
        </span>
    @endif
</a>
