<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="ph-user me-2"></i>Thông tin tài khoản</h5>
    </div>

    <div class="card-body">
        <form action="#">
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


            <div class="text-end">
                <button wire:loading wire:target="submit" class="btn btn-primary">
                    <i class="ph-circle-notch spinner"></i>
                    Lưu
                </button>
                <button wire:click="submit" wire:loading.remove class="btn btn-primary">
                    <i class="ph-floppy-disk"></i>
                    Lưu
                </button>
            </div>
        </form>
    </div>
</div>
