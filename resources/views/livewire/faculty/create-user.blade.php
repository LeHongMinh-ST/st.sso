<div>
    <div class="card">
        <div class="card-header bold">
            <i class="ph-user-plus"></i>
            Thêm người dùng vào khoa
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" wire:model="user_name" placeholder="Nhập tên đăng nhập" class="form-control @error('user_name') is-invalid @enderror">
                        @error('user_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="text" wire:model="email" placeholder="Nhập email" class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Họ <span class="text-danger">*</span></label>
                        <input type="text" wire:model="last_name" placeholder="Nhập họ" class="form-control @error('last_name') is-invalid @enderror">
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Tên <span class="text-danger">*</span></label>
                        <input type="text" wire:model="first_name" placeholder="Nhập tên" class="form-control @error('first_name') is-invalid @enderror">
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" wire:model="phone" placeholder="Nhập số điện thoại" class="form-control @error('phone') is-invalid @enderror">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Loại tài khoản</label>
                        <select wire:model.live="role" class="form-select">
                            @foreach (\App\Enums\Role::getDescription() as $roleItem => $description)
                                @if($roleItem != \App\Enums\Role::SuperAdmin->value)
                                    <option value="{{ $roleItem }}">{{ $description }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @if($role == \App\Enums\Role::Student || $role == \App\Enums\Role::Officer)
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">{{ $role == \App\Enums\Role::Student ? 'Mã sinh viên' : 'Mã giảng viên' }} @if($role == \App\Enums\Role::Student) <span class="text-danger">*</span> @endif</label>
                        <input type="text" wire:model="code" placeholder="{{ $role == \App\Enums\Role::Student ? 'Nhập mã sinh viên' : 'Nhập mã giảng viên' }}" class="form-control @error('code') is-invalid @enderror">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="ph-info me-2"></i> Mật khẩu mặc định là "password". Người dùng sẽ được yêu cầu đổi mật khẩu khi đăng nhập lần đầu.
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button wire:loading wire:target="submit" class="btn btn-primary fw-semibold">
                <i class="ph-circle-notch spinner fw-semibold"></i>
                Lưu
            </button>
            <button wire:click="submit" wire:loading.remove class="btn btn-primary fw-semibold">
                <i class="ph-floppy-disk fw-semibold"></i>
                Lưu
            </button>
        </div>
    </div>
</div>
