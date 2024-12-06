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
                            </a>
                        </li>
                        <li class="{{ $page == 'izine-lapor-skd' ? 'active' : '' }}">
                            <a href="{{ route('izine.lapor-skd') }}">
                                <i class="icon-Bed"><span class="path1"></span><span class="path2"></span></i>
                                <span>Lapor SKD</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasRole('atasan') && auth()->user()->karyawan->posisi[0]->jabatan_id <= 4)
                        <li class="{{ $page == 'izine-approval-izin' ? 'active' : '' }}">
                            <a href="{{ route('izine.approval-izin') }}">
                                <i class="icon-Double-check"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Approval Izin</span>
                            </a>
                        </li>
                        <li class="{{ $page == 'izine-approval-skd' ? 'active' : '' }}">
                            <a href="{{ route('izine.approval-skd') }}">
                                <i class="icon-Double-check"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Approval SKD</span>
                            </a>
                        </li>
                    @endif
                    {{-- @if (auth()->user()->hasRole('atasan') || auth()->user()->hasRole('personalia'))
                        <li class="{{ $page == 'izine-pengajuan-izin' ? 'active' : '' }}">
                            <a href="{{ route('izine.pengajuan-izin') }}">
                                <i class="icon-Adress-book1"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Izin Tidak Masuk</span>
                            </a>
                        </li>
                        <li class="{{ $page == 'izine-pengajuan-izin' ? 'active' : '' }}">
                            <a href="{{ route('izine.pengajuan-izin') }}">
                                <i class="icon-Adress-book1"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Izin Setengah Hari</span>
                            </a>
                        </li>
                        <li class="{{ $page == 'izine-pengajuan-izin' ? 'active' : '' }}">
                            <a href="{{ route('izine.pengajuan-izin') }}">
                                <i class="icon-Adress-book1"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Izin Sakit</span>
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
