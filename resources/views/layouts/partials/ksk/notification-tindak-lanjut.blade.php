<a href="{{ route('ksk.tindak-lanjut') }}">
    <i class="icon-Door-open"><span class="path1"></span><span class="path2"></span></i>
    <span>Tindak Lanjut</span>
    @if ($total_tindak_lanjut > 0)
        <span class="pull-right-container" style="right:10px!important; top:55%!important; margin-top:-13px!important;">
            <div class="badge bg-danger m-0" style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                {{ $total_tindak_lanjut }}
            </div>
        </span>
    @endif
</a>
