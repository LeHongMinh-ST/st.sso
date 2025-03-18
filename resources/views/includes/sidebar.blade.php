<div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg">

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- Sidebar header -->
        <div class="sidebar-section">
            <div class="sidebar-section-body d-flex justify-content-center">
                <h5 class="my-auto sidebar-resize-hide flex-grow-1">ST Single Sign-On</h5>

                <div>
                    <button type="button"
                            class="border-transparent btn btn-flat-white btn-icon btn-sm rounded-pill sidebar-control sidebar-main-resize d-none d-lg-inline-flex">
                        <i class="ph-arrows-left-right"></i>
                    </button>

                    <button type="button"
                            class="border-transparent btn btn-flat-white btn-icon btn-sm rounded-pill sidebar-mobile-main-toggle d-lg-none">
                        <i class="ph-x"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- /sidebar header -->


        <!-- Main navigation -->
        <div class="sidebar-section">
            <ul class="nav nav-sidebar" data-nav-type="accordion">
                <li class="nav-item-header">
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="ph-house"></i>
                        <span>Bảng điều khiển</span>
                    </a>
                </li>


                @if (auth()->user()->isSuperAdmin())
                    <li class="nav-item-header">
                        <div class="opacity-50 text-uppercase fs-sm lh-sm sidebar-resize-hide">Ứng dụng</div>
                        <i class="ph-dots-three sidebar-resize-show"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('client.index') }}"
                        class="nav-link {{ request()->routeIs('client.*') ? 'active' : '' }}">
                            <i class="ph-package"></i>
                            <span>Ứng dụng SSO</span>
                        </a>
                    </li>

                    <li class="nav-item-header">
                        <div class="opacity-50 text-uppercase fs-sm lh-sm sidebar-resize-hide">Hệ thống</div>
                        <i class="ph-dots-three sidebar-resize-show"></i>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('faculty.index') }}"
                           class="nav-link {{ request()->routeIs('faculty.*') ? 'active' : '' }}">
                            <i class="ph-buildings"></i>
                            <span>Khoa</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('user.index') }}"
                           class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}">
                            <i class="ph-user"></i>
                            <span>Người dùng</span>
                        </a>
                    </li>

                 
                @endif


            </ul>
        </div>
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->

</div>
