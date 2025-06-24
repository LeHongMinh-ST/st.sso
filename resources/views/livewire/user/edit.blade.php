<div>
    <div class="row">
        <div class="col-md-9 col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bold">
                            <i class="ph-user"></i>
                            Thông tin chung
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tên đăng nhập</label>
                                        <input type="text" wire:model="user_name" placeholder="Nhập tên đăng nhập" class="form-control">
                                        @error('user_name')
                                            <label id="error-user_name"
                                                   class="validation-error-label text-danger"
                                                   for="user_name">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="text" wire:model="email" placeholder="Nhập email" class="form-control">
                                        @error('email')
                                            <label id="error-email"
                                                   class="validation-error-label text-danger"
                                                   for="email">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Họ</label>
                                        <input type="text" wire:model="last_name" placeholder="Nhập họ" class="form-control">
                                        @error('last_name')
                                            <label id="error-last_name"
                                                   class="validation-error-label text-danger"
                                                   for="last_name">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tên</label>
                                        <input type="text" wire:model="first_name" placeholder="Nhập tên" class="form-control">
                                        @error('first_name')
                                            <label id="error-first_name"
                                                   class="validation-error-label text-danger"
                                                   for="first_name">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Số điện thoại</label>
                                        <input type="text" wire:model="phone" placeholder="Nhập số điện thoại" class="form-control">
                                        @error('phone')
                                            <label id="error-phone"
                                                   class="validation-error-label text-danger"
                                                   for="phone">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header bold">
                            <i class="ph-buildings"></i>
                            Thông tin khác
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Khoa</label>
                                        <select wire:model.live="faculty_id" class="form-select">
                                            <option value="">Không có</option>
                                            @foreach ($faculties as $faculty)
                                                <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Loại người dùng</label>
                                        <select wire:model="role" class="form-select">
                                            @foreach (\App\Enums\Role::getDescription() as $roleItem => $description)
                                                <option value="{{ $roleItem }}">{{ $description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if($role == \App\Enums\Role::Student || $role == \App\Enums\Role::Officer)
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $role == \App\Enums\Role::Student ? 'Mã sinh viên' : 'Mã giảng viên' }} @if($role == \App\Enums\Role::Student || $role == \App\Enums\Role::Officer) <span class="text-danger">*</span> @endif</label>
                                        <input type="text" wire:model="code" placeholder="{{ $role == \App\Enums\Role::Student ? 'Nhập mã sinh viên' : 'Nhập mã giảng viên' }}" class="form-control @error('code') is-invalid @enderror">
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col">
                                    <label for="description" class="col-form-label">
                                        Trạng thái
                                    </label>
                                    <div class="mb-2 form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:click="toggleStatus" {{ $status == \App\Enums\Status::Active ? 'checked' : '' }}>
                                        {{ $status->getLabel() }}
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
                    <a href="{{ route('user.index') }}" type="button" class="btn btn-warning flex-fill fw-semibold"><i
                           class="ph-arrow-counter-clockwise fw-semibold"></i> Trở lại</a>
                </div>
            </div>
        </div>
    </div>
</div>
