<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">Cutie Menu</li>
                    <li class="{{ $page == 'cutie-dashboard' ? 'active' : '' }}">
                        <a href="{{ route('cutie.dashboard') }}">
                            <i class="icon-Layout-4-blocks"><span class="path1"></span><span class="path2"></span></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'cutie-pengajuan-cuti' ? 'active' : '' }}">
                        <a href="{{ route('cutie.pengajuan-cuti') }}">
                            <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                            <span>Pengajuan Cuti</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'cutie-member-cuti' ? 'active' : '' }}">
                        <a href="{{ route('cutie.member-cuti') }}">
                            <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                            <span>Member Cuti</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'cutie-personalia-cuti' ? 'active' : '' }}">
                        <a href="{{ route('cutie.personalia-cuti') }}">
                            <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                            <span>List Cuti</span>
                        </a>
                    </li>
                    {{-- <li class="{{ $page == 'cutie-kontrak' ? 'active' : '' }}">
                        <a href="{{ route('cutie.kontrak') }}">
                            <i class="icon-File"><span class="path1"></span><span class="path2"></span><span
                                    class="path3"></span></i>
                            <span>Kontrak Karyawan</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'cutie-turnover' ? 'active' : '' }}">
                        <a href="{{ route('cutie.turnover') }}">
                            <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                            <span>Turnover Karyawan</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'cutie-posisi' ? 'active' : '' }}">
                        <a href="{{ route('cutie.posisi') }}">
                            <i class="icon-Library"><span class="path1"></span><span class="path2"></span></i>
                            <span>Susunan & Posisi</span>
                        </a>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i span class="icon-Layout-grid"><span class="path1"></span><span
                                    class="path2"></span></i>
                            <span>Master Data</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-right pull-right"></i>
                            </span>
                        </a>
                        <ul
                            class="treeview-menu {{ $page == 'cutie-organisasi' || $page == 'cutie-divisi' || $page == 'cutie-departemen' || $page == 'cutie-seksi' || $page == 'cutie-grup' || $page == 'cutie-jabatan' ? 'active' : '' }}">
                            <li class="{{ $page == 'cutie-organisasi' ? 'active' : '' }}"><a
                                    href="{{ route('cutie.organisasi') }}"><i class="icon-Commit"><span
                                            class="path1"></span><span class="path2"></span></i>Organisasi</a>
                            </li>
                            <li class="{{ $page == 'cutie-divisi' ? 'active' : '' }}"><a
                                    href="{{ route('cutie.divisi') }}"><i class="icon-Commit"><span
                                            class="path1"></span><span class="path2"></span></i>Divisi</a></li>
                            <li class="{{ $page == 'cutie-departemen' ? 'active' : '' }}"><a
                                    href="{{ route('cutie.departemen') }}"><i class="icon-Commit"><span
                                            class="path1"></span><span class="path2"></span></i>Departemen</a>
                            </li>
                            <li class="{{ $page == 'cutie-seksi' ? 'active' : '' }}"><a
                                    href="{{ route('cutie.seksi') }}"><i class="icon-Commit"><span
                                            class="path1"></span><span class="path2"></span></i>Seksi</a>
                            </li>
                            <li class="{{ $page == 'cutie-grup' ? 'active' : '' }}"><a
                                    href="{{ route('cutie.grup') }}"><i class="icon-Commit"><span
                                            class="path1"></span><span class="path2"></span></i>Grup</a>
                            </li>
                            <li class="{{ $page == 'cutie-jabatan' ? 'active' : '' }}"><a
                                    href="{{ route('cutie.jabatan') }}"><i class="icon-Commit"><span
                                            class="path1"></span><span class="path2"></span></i>Jabatan</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ $page == 'cutie-export' ? 'active' : '' }}">
                        <a href="{{ route('cutie.export') }}">
                            <i class="icon-Chat-check"><span class="path1"></span><span class="path2"></span></i>
                            <span>Export Data</span>
                        </a>
                    </li> --}}
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
