<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">Master Data Menu</li>
                    <li class="{{ $page == 'masterdata-dashboard' ? 'active' : '' }}">
                        <a href="{{ route('master-data.dashboard') }}">
                            <i class="icon-Layout-4-blocks"><span class="path1"></span><span class="path2"></span></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'masterdata-karyawan' ? 'active' : '' }}">
                        <a href="{{ route('master-data.karyawan') }}">
                            <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                            <span>Karyawan</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'masterdata-kontrak' ? 'active' : '' }}">
                        <a href="{{ route('master-data.kontrak') }}">
                            <i class="icon-File"><span class="path1"></span><span class="path2"></span><span
                                    class="path3"></span></i>
                            <span>Kontrak</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'masterdata-turnover' ? 'active' : '' }}">
                        <a href="{{ route('master-data.turnover') }}">
                            <i class="icon-Outgoing-box"><span class="path1"></span><span class="path2"></span></i>
                            <span>Karyawan Keluar</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'masterdata-posisi' ? 'active' : '' }}">
                        <a href="{{ route('master-data.posisi') }}">
                            <i class="icon-Library"><span class="path1"></span><span class="path2"></span></i>
                            <span>Susunan & Posisi</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'masterdata-grup' ? 'active' : '' }}">
                        <a href="{{ route('master-data.grup') }}">
                            <i class="icon-Time-schedule"><span class="path1"></span><span class="path2"></span></i>
                            <span>Shift & Pola</span>
                        </a>
                    </li>
                    {{-- <li class="treeview">
                        <a href="#">
                            <i span class="icon-Layout-grid"><span class="path1"></span><span
                                    class="path2"></span></i>
                            <span>Master Data</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-right pull-right"></i>
                            </span>
                        </a>
                        <ul
                            class="treeview-menu {{ $page == 'masterdata-organisasi' || $page == 'masterdata-divisi' || $page == 'masterdata-departemen' || $page == 'masterdata-seksi' || $page == 'masterdata-grup' || $page == 'masterdata-jabatan' ? 'active' : '' }}">
                            <li class="{{ $page == 'masterdata-organisasi' ? 'active' : '' }}"><a
                                    href="{{ route('master-data.organisasi') }}"><i class="icon-Commit"><span
                                            class="path1"></span><span class="path2"></span></i>Organisasi</a>
                            </li>
                            <li class="{{ $page == 'masterdata-divisi' ? 'active' : '' }}"><a
                                    href="{{ route('master-data.divisi') }}"><i class="icon-Commit"><span
                                            class="path1"></span><span class="path2"></span></i>Divisi</a></li>
                            <li class="{{ $page == 'masterdata-departemen' ? 'active' : '' }}"><a
                                    href="{{ route('master-data.departemen') }}"><i class="icon-Commit"><span
                                            class="path1"></span><span class="path2"></span></i>Departemen</a>
                            </li>
                            <li class="{{ $page == 'masterdata-seksi' ? 'active' : '' }}"><a
                                    href="{{ route('master-data.seksi') }}"><i class="icon-Commit"><span
                                            class="path1"></span><span class="path2"></span></i>Seksi</a>
                            </li>
                            <li class="{{ $page == 'masterdata-grup' ? 'active' : '' }}"><a
                                    href="{{ route('master-data.grup') }}"><i class="icon-Commit"><span
                                            class="path1"></span><span class="path2"></span></i>Grup</a>
                            </li>
                            <li class="{{ $page == 'masterdata-jabatan' ? 'active' : '' }}"><a
                                    href="{{ route('master-data.jabatan') }}"><i class="icon-Commit"><span
                                            class="path1"></span><span class="path2"></span></i>Jabatan</a>
                            </li>
                        </ul>
                    </li> --}}
                    <li class="{{ $page == 'masterdata-event' ? 'active' : '' }}">
                        <a href="{{ route('master-data.event') }}">
                            <i class="icon-Building"><span class="path1"></span><span class="path2"></span></i>
                            <span>Kalender Event</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'masterdata-template' ? 'active' : '' }}">
                        <a href="{{ route('master-data.template') }}">
                            <i class="icon-Tools"><span class="path1"></span><span class="path2"></span></i>
                            <span>Template Surat</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'masterdata-export' ? 'active' : '' }}">
                        <a href="{{ route('master-data.export') }}">
                            <i class="icon-Chat-check"><span class="path1"></span><span class="path2"></span></i>
                            <span>Export Data</span>
                        </a>
                    </li>
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
