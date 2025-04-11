<div>
    <div class="row">
        <div class="col-md-9 col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bold">
                            <i class="ph-shield"></i>
                            Quản lý vai trò
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label">Vai trò</label>
                                    <div class="row mb-4">
                                        @foreach($roles as $role)
                                            <div class="col-md-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" wire:model="selectedRoles" value="{{ $role->id }}" id="role_{{ $role->id }}">
                                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                                        {{ $role->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label">Quyền trực tiếp</label>
                                    <p class="text-muted">Các quyền được gán trực tiếp cho người dùng, không thông qua vai trò.</p>
                                    
                                    <div class="row">
                                        @foreach($availablePermissions as $group => $permissions)
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0 text-capitalize">{{ $group }}</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @foreach($permissions as $permission)
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" wire:model="directPermissions" value="{{ $permission['name'] }}" id="permission_{{ $permission['id'] }}">
                                                                <label class="form-check-label" for="permission_{{ $permission['id'] }}">
                                                                    @php
                                                                        $action = explode('.', $permission['name'])[1];
                                                                        switch($action) {
                                                                            case 'view':
                                                                                $actionText = 'Xem';
                                                                                break;
                                                                            case 'create':
                                                                                $actionText = 'Thêm';
                                                                                break;
                                                                            case 'edit':
                                                                                $actionText = 'Sửa';
                                                                                break;
                                                                            case 'delete':
                                                                                $actionText = 'Xóa';
                                                                                break;
                                                                            case 'reset_password':
                                                                                $actionText = 'Reset mật khẩu';
                                                                                break;
                                                                            case 'assign_permissions':
                                                                                $actionText = 'Phân quyền';
                                                                                break;
                                                                            case 'assign_users':
                                                                                $actionText = 'Gán người dùng';
                                                                                break;
                                                                            case 'user':
                                                                            case 'faculty':
                                                                            case 'client':
                                                                            case 'role':
                                                                            case 'auth':
                                                                                $actionText = ucfirst($action);
                                                                                break;
                                                                            default:
                                                                                $actionText = $action;
                                                                        }
                                                                    @endphp
                                                                    {{ $actionText }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="card">
                <div class="card-header bold">
                    Hành động
                </div>
                <div class="gap-2 card-body d-flex justify-content-center">
                    <button wire:loading wire:target="updateRoles" class="shadow btn btn-primary fw-semibold flex-fill">
                        <i class="ph-circle-notch spinner fw-semibold"></i>
                        Lưu
                    </button>
                    <button wire:click="updateRoles" wire:loading.remove class="shadow btn btn-primary fw-semibold flex-fill">
                        <i class="ph-floppy-disk fw-semibold"></i>
                        Lưu
                    </button>
                    <a href="{{ route('user.show', $user->id) }}" type="button" class="btn btn-warning flex-fill fw-semibold"><i
                           class="ph-arrow-counter-clockwise fw-semibold"></i> Trở lại</a>
                </div>
            </div>
        </div>
    </div>
</div>
