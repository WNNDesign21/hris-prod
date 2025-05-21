<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">Lembur-E Menu</li>
                    @if (auth()->user()->hasRole('personalia') || auth()->user()->karyawan->posisi[0]->jabatan_id <= 4)
                        <li class="{{ $page == 'lembure-dashboard' ? 'active' : '' }}">
                            <a href="{{ route('lembure.dashboard') }}">
                                <i class="icon-Chart-bar2"><span class="path1"></span><span class="path2"></span></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasRole('personalia') || auth()->user()->karyawan->posisi[0]->jabatan_id <= 4)
                        <li class="{{ $page == 'lembure-detail-lembur' ? 'active' : '' }}">
                            <a href="{{ route('lembure.detail-lembur') }}">
                                <i class="icon-Stairs"><span class="path1"></span><span class="path2"></span></i>
                                <span>Detail Lembur</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->karyawan && ($lembure['is_leader'] || !$lembure['has_leader']))
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
                    @endif
                    {{-- Versi Sebelumnya --}}
                    {{-- @if (auth()->user()->hasRole('personalia'))
                        <li class="{{ $page == 'lembure-bypass-lembur' ? 'active' : '' }}">
                            <a href="{{ route('lembure.bypass-lembur') }}">
                                <i class="icon-Thunder1"><span class="path1"></span><span class="path2"></span></i>
                                <span>Bypass Lembur</span>
                            </a>
                        </li>
                    @endif --}}
                    {{-- @if (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id <= 3 || (auth()->user()->karyawan->posisi[0]->jabatan_id == 4 && !$lembure['has_dept_head'])))
                        <li class="{{ $page == 'lembure-bypass-lembur' ? 'active' : '' }}">
                            <a href="{{ route('lembure.bypass-lembur') }}">
                                <i class="icon-Thunder1"><span class="path1"></span><span class="path2"></span></i>
                                <span>Bypass Lembur</span>
                            </a>
                        </li>
                    @endif --}}
                    {{-- Versi Pak Kuncara --}}
                    {{-- @if (auth()->user()->hasRole('atasan') && auth()->user()->karyawan->posisi[0]->jabatan_id == 2 && auth()->user()->karyawan->posisi[0]->divisi_id == 3)
                        <li class="{{ $page == 'lembure-bypass-lembur' ? 'active' : '' }}">
                            <a href="{{ route('lembure.bypass-lembur') }}">
                                <i class="icon-Thunder1"><span class="path1"></span><span class="path2"></span></i>
                                <span>Bypass Lembur</span>
                            </a>
                        </li>
                    @endif --}}
                    @if (auth()->user()->hasRole('personalia') ||
                            (auth()->user()->karyawan &&
                                (auth()->user()->karyawan->posisi[0]->jabatan_id == 4 ||
                                    auth()->user()->karyawan->posisi[0]->jabatan_id == 3 ||
                                    auth()->user()->karyawan->posisi[0]->jabatan_id == 2)))
                        <li
                            class="{{ $page == 'lembure-approval-lembur' ? 'active' : '' }} notification-approval-lembur">
                            <a href="{{ route('lembure.approval-lembur') }}">
                                <i class="icon-Double-check"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Approval Lembur</span>
                                @if ($lembure['approval_lembur'] > 0)
                                    <span class="pull-right-container"
                                        style="right:10px!important; top:55%!important; margin-top:-13px!important;">
                                        <div class="badge bg-danger m-0"
                                            style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                                            {{ $lembure['approval_lembur'] }}
                                        </div>
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasRole('atasan') && auth()->user()->karyawan->posisi[0]->jabatan_id == 1)
                        <li class="{{ $page == 'lembure-review-lembur' ? 'active' : '' }} notification-review-lembur">
                            <a href="{{ route('lembure.review-lembur') }}">
                                <i class="icon-Double-check"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Review Lembur</span>
                                @if ($lembure['review_lembur'] > 0)
                                    <span class="pull-right-container"
                                        style="right:10px!important; top:55%!important; margin-top:-13px!important;">
                                        <div class="badge bg-danger m-0"
                                            style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                                            {{ $lembure['review_lembur'] }}
                                        </div>
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasRole('personalia'))
                        <li class="{{ $page == 'lembure-bypass-lembur' ? 'active' : '' }}">
                            <a href="{{ route('lembure.bypass-lembur') }}">
                                <i class="icon-Thunder1"><span class="path1"></span><span class="path2"></span></i>
                                <span>Bypass Lembur</span>
                            </a>
                        </li>
                        <li class="{{ $page == 'lembure-setting-upah-lembur' ? 'active' : '' }}">
                            <a href="{{ route('lembure.setting-upah-lembur') }}">
                                <i class="icon-Settings"><span class="path1"></span><span class="path2"></span></i>
                                <span>Setting Gaji Lembur</span>
                            </a>
                        </li>
                        <li class="{{ $page == 'lembure-setting-lembur' ? 'active' : '' }}">
                            <a href="{{ route('lembure.setting-lembur') }}">
                                <i class="icon-Tools"><span class="path1"></span><span class="path2"></span></i>
                                <span>Setting Lembur</span>
                            </a>
                        </li>
                        <li class="{{ $page == 'lembure-setting-gaji-departemen' ? 'active' : '' }}">
                            <a href="{{ route('lembure.setting-gaji-departemen') }}">
                                <i class="icon-Building"><span class="path1"></span><span class="path2"></span></i>
                                <span>Setting Gaji Departemen</span>
                            </a>
                        </li>
                        <li class="{{ $page == 'lembure-export-report-lembur' ? 'active' : '' }}">
                            <a href="{{ route('lembure.export-report-lembur') }}">
                                <i class="icon-Export"><span class="path1"></span><span class="path2"></span></i>
                                <span>Export Report Lembur</span>
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
