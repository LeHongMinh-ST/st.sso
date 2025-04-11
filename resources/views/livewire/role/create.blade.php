<div>
    <div class="row">
        <div class="col-md-9 col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bold">
                            <i class="ph-shield"></i>
                            Thông tin vai trò
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tên vai trò <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="name" placeholder="Nhập tên vai trò" class="form-control @error('name') is-invalid @enderror">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label">Quyền hạn</label>
                                    <div class="row">
                                        @foreach($permissions as $group => $items)
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0 text-capitalize">{{ $group }}</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @foreach($items as $permission)
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" wire:model="selectedPermissions" value="{{ $permission->name }}" id="permission_{{ $permission->id }}">
                                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                                    @php
                                                                        $action = explode('.', $permission->name)[1];
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
                    <button wire:loading wire:target="submit" class="shadow btn btn-primary fw-semibold flex-fill">
                        <i class="ph-circle-notch spinner fw-semibold"></i>
                        Lưu
                    </button>
                    <button wire:click="submit" wire:loading.remove class="shadow btn btn-primary fw-semibold flex-fill">
                        <i class="ph-floppy-disk fw-semibold"></i>
                        Lưu
                    </button>
                    <a href="{{ route('role.index') }}" type="button" class="btn btn-warning flex-fill fw-semibold"><i
                           class="ph-arrow-counter-clockwise fw-semibold"></i> Trở lại</a>
                </div>
            </div>
        </div>
    </div>
</div>
