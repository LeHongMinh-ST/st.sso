<div class="d-lg-flex align-items-lg-start">

    <!-- Left sidebar component -->
    <div class="sidebar sidebar-component sidebar-expand-lg bg-transparent shadow-none me-lg-3">

        <!-- Sidebar content -->
        <div class="sidebar-content">

            <!-- Navigation -->
            <div class="card">
                <ul class="nav nav-sidebar" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#" class="nav-link @if($tab == 'profile') active @endif" wire:click="selectTab('profile')" data-bs-toggle="tab" aria-selected="@if($tab == 'profile') true @else false @endif" role="tab">
                            <i class="ph-user me-2"></i>
                            Thông tin tài khoản
                        </a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a href="#" class="nav-link @if($tab == 'password') active @endif" wire:click="selectTab('password')" data-bs-toggle="tab" aria-selected="@if($tab == 'password') true @else false @endif" tabindex="-1" role="tab">
                            <i class="ph-lock me-2"></i>
                            Đổi mật khẩu
                        </a>
                    </li>

                    <li class="nav-item-divider"></li>
                    <li class="nav-item" role="presentation">
                        <form action="{{ route('handleLogout') }}" class="nav-link" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item ">
                                <i class="ph-sign-out me-2"></i>
                                Đăng xuất
                            </button>
                        </form>

                    </li>
                </ul>
            </div>
            <!-- /navigation -->

        </div>
        <!-- /sidebar content -->

    </div>
    <!-- /left sidebar component -->

    <!-- Right content -->
    <div class="tab-content flex-fill">
        <div class="tab-pane fade @if($tab == 'profile') active show @endif " id="profile" role="tabpanel">
            <livewire:profile.general />
        </div>
        <div class="tab-pane fade @if($tab == 'password') active show @endif" id="password" role="tabpanel">
            <livewire:profile.password />
        </div>
    </div>
    <!-- /right content -->

</div>
