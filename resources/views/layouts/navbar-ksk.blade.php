<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">KSK-E Menu</li>
                    @if (auth()->user()->hasRole('personalia'))
                        <li class="{{ $page == 'ksk-release' ? 'active' : '' }} notification-release">
                            <a href="{{ route('ksk.release') }}">
                                <i class="icon-Book"><span class="path1"></span><span class="path2"></span></i>
                                <span>Release KSK</span>
                                @if ($ksk['total_release_ksk'] > 0)
                                    <span class="pull-right-container"
                                        style="right:10px!important; top:55%!important; margin-top:-13px!important;">
                                        <div class="badge bg-danger m-0"
                                            style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                                            {{ $ksk['total_release_ksk'] }}
                                        </div>
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasRole('atasan') || auth()->user()->hasRole('personalia'))
                        <li class="{{ $page == 'ksk-approval' ? 'active' : '' }} notification-approval">
                            <a href="{{ route('ksk.approval') }}">
                                <i class="icon-Double-check"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Approval KSK</span>
                                @if ($ksk['total_approval_ksk'] > 0)
                                    <span class="pull-right-container"
                                        style="right:10px!important; top:55%!important; margin-top:-13px!important;">
                                        <div class="badge bg-danger m-0"
                                            style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                                            {{ $ksk['total_approval_ksk'] }}
                                        </div>
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasRole('atasan') || auth()->user()->hasRole('personalia'))
                        <li
                            class="{{ $page == 'ksk-cleareance-release' ? 'active' : '' }} notification-cleareance-release">
                            <a href="{{ route('ksk.cleareance.release') }}">
                                <i class="icon-Shield-check"><span class="path1"></span><span
                                        class="path2"></span></i>
                                <span>Release Cleareance</span>
                                @if ($ksk['total_release_cleareance'] > 0)
                                    <span class="pull-right-container"
                                        style="right:10px!important; top:55%!important; margin-top:-13px!important;">
                                        <div class="badge bg-danger m-0"
                                            style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                                            {{ $ksk['total_release_cleareance'] }}
                                        </div>
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif
                    @role('atasan')
                        <li
                            class="{{ $page == 'ksk-cleareance-approval' ? 'active' : '' }} notification-cleareance-approval">
                            <a href="{{ route('ksk.cleareance.approval') }}">
                                <i class="icon-Double-check"><span class="path1"></span><span class="path2"></span></i>
                                <span>Approval Cleareance</span>
                                {{-- @if ($ksk['total_approval_ksk'] > 0)
                                <span class="pull-right-container"
                                    style="right:10px!important; top:55%!important; margin-top:-13px!important;">
                                    <div class="badge bg-danger m-0"
                                        style="border-radius: 20%; line-height: normal; height:100%; width:100%;">
                                        {{ $ksk['total_approval_ksk'] }}
                                    </div>
                                </span>
                                @endif --}}
                            </a>
                        </li>
                    @endrole
                    @role('personalia')
                        <li class="{{ $page == 'ksk-setting' ? 'active' : '' }} notification-setting">
                            <a href="{{ route('ksk.setting') }}">
                                <i class="icon-Tools"><span class="path1"></span><span class="path2"></span></i>
                                <span>Setting</span>
                            </a>
                        </li>
                    @endrole
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
