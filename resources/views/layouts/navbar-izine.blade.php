<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">Izin-E Menu</li>
                    @if (auth()->user()->hasRole('atasan') || auth()->user()->hasRole('member'))
                        <li class="{{ $page == 'izine-pengajuan-izin' ? 'active' : '' }}">
                            <a href="{{ route('izine.pengajuan-izin') }}">
                                <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                                <span>Pengajuan Izin</span>
                                @if ($izine['pengajuan_izin'] > 0)
                                    <span class="pull-right-container"
                                        style="right:10px!important; top:55%!important; margin-top:-13px!important;">
                                        <div class="badge bg-danger m-0"
                                            style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                                            {{ $izine['pengajuan_izin'] }}
                                        </div>
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li class="{{ $page == 'izine-lapor-skd' ? 'active' : '' }}">
                            <a href="{{ route('izine.lapor-skd') }}">
                                <i class="icon-Bed"><span class="path1"></span><span class="path2"></span></i>
                                <span>Lapor SKD</span>
                                @if ($izine['laporan_skd'] > 0)
                                    <span class="pull-right-container"
                                        style="right:10px!important; top:55%!important; margin-top:-13px!important;">
                                        <div class="badge bg-danger m-0"
                                            style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                                            {{ $izine['laporan_skd'] }}
                                        </div>
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasRole('personalia') ||
                            (auth()->user()->hasRole('atasan') && auth()->user()->karyawan->posisi[0]->jabatan_id <= 4))
                        <li class="{{ $page == 'izine-approval-izin' ? 'active' : '' }}">
                            <a href="{{ route('izine.approval-izin') }}">
                                <i class="icon-Double-check"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Approval Izin</span>
                                @if ($izine['approval_izin'] > 0)
                                    <span class="pull-right-container"
                                        style="right:10px!important; top:55%!important; margin-top:-13px!important;">
                                        <div class="badge bg-danger m-0"
                                            style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                                            {{ $izine['approval_izin'] }}
                                        </div>
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li class="{{ $page == 'izine-approval-skd' ? 'active' : '' }}">
                            <a href="{{ route('izine.approval-skd') }}">
                                <i class="icon-Double-check"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Approval SKD</span>
                                @if ($izine['approval_skd'] > 0)
                                    <span class="pull-right-container"
                                        style="right:10px!important; top:55%!important; margin-top:-13px!important;">
                                        <div class="badge bg-danger m-0"
                                            style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                                            {{ $izine['approval_skd'] }}
                                        </div>
                                    </span>
                                @endif
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
