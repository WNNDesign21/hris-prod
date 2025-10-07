<a href="{{ route('lembure.review-lembur') }}">
    <i class="icon-Double-check"><span class="path1"></span><span class="path2"></span></i>
    <span>Review Lembur</span>
    @if ($lembure['review_lembur'] > 0)
        <span class="pull-right-container" style="right:30px!important; top:55%!important; margin-top:-13px!important;">
            <div class="badge bg-danger m-0" style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                {{ $lembure['review_lembur'] }}
            </div>
        </span>
    @endif
</a>
