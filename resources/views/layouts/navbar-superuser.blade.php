<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">Master Data Menu</li>
                    <li class="{{ $page == 'superuser-organisasi' ? 'active' : '' }}">
                        <a href="{{ route('superuser.organisasi') }}">
                            <i class="icon-Building"><span class="path1"></span><span class="path2"></span></i>
                            <span>Organisasi</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'superuser-divisi' ? 'active' : '' }}">
                        <a href="{{ route('superuser.divisi') }}">
                            <i class="icon-Layout-horizontal"><span class="path1"></span><span
                                    class="path2"></span></i>
                            <span>Divisi</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'superuser-departemen' ? 'active' : '' }}">
                        <a href="{{ route('superuser.departemen') }}">
                            <i class="icon-Layout-right-panel-2"><span class="path1"></span><span
                                    class="path2"></span></i>
                            <span>Departemen</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'superuser-seksi' ? 'active' : '' }}">
                        <a href="{{ route('superuser.seksi') }}">
                            <i class="icon-Layout-left-panel-2"><span class="path1"></span><span
                                    class="path2"></span></i>
                            <span>Seksi</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'superuser-jabatan' ? 'active' : '' }}">
                        <a href="{{ route('superuser.jabatan') }}">
                            <i class="icon-Layout-4-blocks"><span class="path1"></span><span class="path2"></span></i>
                            <span>Jabatan</span>
                        </a>
                    </li>
                    <li class="header">System Menu</li>
                    <li class="{{ $page == 'superuser-user' ? 'active' : '' }}">
                        <a href="{{ route('superuser.user') }}">
                            <i class="icon-Add-user"><span class="path1"></span><span class="path2"></span></i>
                            <span>User</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'superuser-activity-log' ? 'active' : '' }}">
                        <a href="{{ route('superuser.activity-log') }}">
                            <i class="icon-Clipboard-list"><span class="path1"></span><span class="path2"></span></i>
                            <span>Activity Log</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'superuser-system-setting' ? 'active' : '' }}">
                        <a href="{{ route('superuser.organisasi') }}">
                            <i class="icon-Settings"><span class="path1"></span><span class="path2"></span></i>
                            <span>Setting</span>
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
