<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">Attendance-E Menu</li>
                    @if (auth()->user()->hasRole('member') || auth()->user()->hasRole('atasan'))
                        <li class="{{ $page == 'attendance-gps' ? 'active' : '' }}">
                            <a href="{{ route('attendance.gps') }}">
                                <i class="icon-Layout-4-blocks"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Presensi TL</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasRole('personalia') || auth()->user()->hasRole('admin-dept'))
                        <li class="{{ $page == 'attendance-presensi' ? 'active' : '' }}">
                            <a href="{{ route('attendance.presensi') }}">
                                <i class="icon-Layout-4-blocks"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Presensi</span>
                            </a>
                        </li>
                        @if (auth()->user()->hasRole('personalia'))
                            <li class="{{ $page == 'attendance-scanlog' ? 'active' : '' }}">
                                <a href="{{ route('attendance.scanlog') }}">
                                    <i class="icon-Sign-in"><span class="path1"></span><span class="path2"></span></i>
                                    <span>Scanlog</span>
                                </a>
                            </li>
                            <li class="{{ $page == 'attendance-device' ? 'active' : '' }}">
                                <a href="{{ route('attendance.device') }}">
                                    <i class="icon-Router1"><span class="path1"></span><span class="path2"></span></i>
                                    <span>Device</span>
                                </a>
                            </li>
                        @endif
                        <li class="{{ $page == 'attendance-shiftgroup' ? 'active' : '' }}">
                            <a href="{{ route('attendance.shiftgroup') }}">
                                <i class="icon-Group"><span class="path1"></span><span class="path2"></span></i>
                                <span>Shift Group</span>
                            </a>
                        </li>
                        <li class="{{ $page == 'attendance-rekap' ? 'active' : '' }}">
                            <a href="{{ route('attendance.rekap') }}">
                                <i class="icon-Chat-check"><span class="path1"></span><span class="path2"></span></i>
                                <span>Rekap Kehadiran</span>
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
