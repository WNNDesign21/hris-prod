<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">TugasLuar-E Menu</li>
                    @if (auth()->user()->hasRole('atasan') || auth()->user()->hasRole('member'))
                        <li class="{{ $page == 'tugasluare-pengajuan' ? 'active' : '' }}">
                            <a href="{{ route('tugasluare.pengajuan') }}">
                                <i class="icon-Book"><span class="path1"></span><span class="path2"></span></i>
                                <span>Pengajuan TL</span>
                            </a>
                        </li>
                        {{-- <li class="{{ $page == 'tugasluare-claim' ? 'active' : '' }}">
                            <a href="{{ route('tugasluare.claim') }}">
                                <i class="icon-Money"><span class="path1"></span><span class="path2"></span></i>
                                <span>Claim TL</span>
                            </a>
                        </li> --}}
                    @endif
                    @if (auth()->user()->hasRole('atasan') || auth()->user()->hasRole('personalia') || auth()->user()->hasRole('security'))
                        <li class="{{ $page == 'tugasluare-approval' ? 'active' : '' }}">
                            <a href="{{ route('tugasluare.approval') }}">
                                <i class="icon-Double-check"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Approval TL</span>
                            </a>
                        </li>
                        {{-- <li class="{{ $page == '#' ? 'active' : '' }}">
                            <a href="{{ route('tugasluare.pengajuan') }}">
                                <i class="icon-Double-check"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Approval Claim</span>
                            </a>
                        </li> --}}
                    @endif
                    {{-- CONTOH --}}
                    {{-- @if (auth()->user()->karyawan && ($lembure['is_leader'] || !$lembure['has_leader']))
                        <li
                            class="{{ $page == 'lembure-pengajuan-lembur' ? 'active' : '' }} notification-planned-pengajuan-lembur">
                            <a href="{{ route('lembure.pengajuan-lembur') }}">
                                <i class="icon-Book"><span class="path1"></span><span class="path2"></span></i>
                                <span>Pengajuan Lembur</span>
                                @if ($lembure['pengajuan_lembur'] > 0)
                                    <span class="pull-right-container"
                                        style="right:10px!important; top:55%!important; margin-top:-13px!important;">
                                        <div class="badge bg-danger m-0"
                                            style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                                            {{ $lembure['pengajuan_lembur'] }}
                                        </div>
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif --}}
                </ul>
            </div>
        </div>
    </section>
    <div class="sidebar-footer d-flex justify-content-center">
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="link"
            data-bs-toggle="tooltip" title="Logout"><span class="icon-Lock-overturning"><span
                    class="path1"></span><span class="path2"></span></span></a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</aside>
