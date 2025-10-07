<a href="{{ route('ksk.cleareance.release') }}">
    <i class="icon-Shield-check"><span class="path1"></span><span class="path2"></span></i>
    <span>Release Cleareance</span>
    @if ($total_release_cleareance > 0)
        <span class="pull-right-container" style="right:10px!important; top:55%!important; margin-top:-13px!important;">
            <div class="badge bg-danger m-0" style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                {{ $total_release_cleareance }}
            </div>
        </span>
    @endif
</a>
