<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">Lembur-E Menu</li>
                    <li class="{{ $page == 'lembure-dashboard' ? 'active' : '' }}">
                        <a href="{{ route('lembure.dashboard') }}">
                            <i class="icon-Layout-4-blocks"><span class="path1"></span><span class="path2"></span></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    @if (auth()->user()->karyawan->settingLembur->jabatan_id == 5)
                        <li class="{{ $page == 'lembure-pengajuan-lembur' ? 'active' : '' }}">
                            <a href="{{ route('lembure.pengajuan-lembur') }}">
                                <i class="icon-Layout-4-blocks"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Pengajuan Lembur</span>
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
