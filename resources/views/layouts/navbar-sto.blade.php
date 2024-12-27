<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">STO Menu</li>
                    <li class="{{ $page == 'sto-input-label' ? 'active' : '' }}">
                        <a href="{{ route('sto.input-label') }}">
                            <i class="ti-pencil-alt"><span class="path1"></span><span class="path2"></span></i>
                            <span>Form Register Label</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'sto-input-hasil' ? 'active' : '' }} ">
                        <a href="{{ route('sto.input-hasil') }}">
                            <i class="ti-receipt"><span class="path1"></span><span class="path2"></span></i>
                            <span>Input Hasil STO</span>
                        </a>
                    </li>
                    <li class="{{ $page == 'sto-compare' ? 'active' : '' }} ">
                        <a href="{{ route('sto.compare') }}">
                            <i class="ti-package"><span class="path1"></span><span class="path2"></span></i>
                            <span>Data STO</span>
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
