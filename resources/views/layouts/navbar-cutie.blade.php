<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">Cutie Menu</li>
                    @if (auth()->user()->hasRole('personalia') || auth()->user()->hasRole('atasan'))
                        <li class="{{ $page == 'cutie-dashboard' ? 'active' : '' }}">
                            <a href="{{ route('cutie.dashboard') }}">
                                <i class="icon-Layout-4-blocks"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    @endif
                    @if (!auth()->user()->hasRole('personalia'))
                        <li class="{{ $page == 'cutie-pengajuan-cuti' ? 'active' : '' }} notification-pengajuan-cuti">
                            <a href="{{ route('cutie.pengajuan-cuti.index') }}">
                                <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                                <span>Pengajuan Cuti</span>
                                @if ($notification['count_my_cutie'] + $notification['count_rejected_cuti'] > 0)
                                    <span class="pull-right-container"
                                        style="right:10px!important; top:55%!important; margin-top:-13px!important;">
                                        <div class="badge bg-danger m-0"
                                            style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                                            {{ $notification['count_my_cutie'] + $notification['count_rejected_cuti'] }}
                                        </div>
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasRole('atasan') || auth()->user()->hasRole('personalia'))
                        <li class="{{ $page == 'cutie-approval-cuti' ? 'active' : '' }} notification-approval-cuti">
                            <a href="{{ route('cutie.approval-cuti.index') }}">
                                <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                                <span>Approval Cuti</span>
                                @if ($notification['count_cutie_approval'] > 0)
                                    <span class="pull-right-container"
                                        style="right:10px!important; top:55%!important; margin-top:-13px!important;">
                                        <div class="badge bg-danger m-0"
                                            style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                                            {{ $notification['count_cutie_approval'] }}
                                        </div>
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li class="{{ $page == 'cutie-bypass-cuti' ? 'active' : '' }}">
                            <a href="{{ route('cutie.bypass-cuti.index') }}">
                                <i class="icon-Direction1"><span class="path1"></span><span class="path2"></span></i>
                                <span>Bypass Cuti</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasRole('personalia'))
                        <li class="{{ $page == 'cutie-setting' ? 'active' : '' }}">
                            <a href="{{ route('cutie.setting-cuti.index') }}">
                                <i class="icon-Tools"><span class="path1"></span><span class="path2"></span></i>
                                <span>Setting Cuti Khusus</span>
                            </a>
                        </li>
                        <li class="{{ $page == 'cutie-export' ? 'active' : '' }}">
                            <a href="{{ route('cutie.export.index') }}">
                                <i class="icon-Chat-check"><span class="path1"></span><span class="path2"></span></i>
                                <span>Export Data</span>
                            </a>
                        </li>
                    @endif
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
