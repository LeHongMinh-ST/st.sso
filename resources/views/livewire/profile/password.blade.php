<div class="card" x-data="{ showPasswords: false }">
    <div class="card-header">
        <h5 class="mb-0"><i class="ph-lock me-2"></i> Đổi mật khẩu</h5>
    </div>

    <div class="card-body">
        <form action="#">
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu hiện tại</label>
                        <input :type="showPasswords ? 'text' : 'password'" wire:model.live="password" id="password" placeholder="Nhập mật khẩu hiện tại" class="form-control">
                        @error('password')
                            <label id="error-password"
                                   class="validation-error-label text-danger"
                                   for="password">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <input :type="showPasswords ? 'text' : 'password'" wire:model.live="new_password" id="new_password" placeholder="Nhập mật khẩu mới" class="form-control">
                        @error('new_password')
                            <label id="error-new_password"
                                   class="validation-error-label text-danger"
                                   for="new_password">{{ $message }}</label>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Nhập lại mật khẩu</label>
                        <input :type="showPasswords ? 'text' : 'password'" wire:model.live="password_confirmation" id="password_confirmation" placeholder="Nhập lại mật khẩu" class="form-control">
                        @error('password_confirmation')
                            <label id="error-password_confirmation"
                                   class="validation-error-label text-danger"
                                   for="password_confirmation">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="showPasswords" x-model="showPasswords">
                    <label class="form-check-label" for="showPasswords">Hiển thị mật khẩu</label>
                </div>

                <div>
                    <button wire:loading wire:target="submit" class="shadow btn btn-primary fw-semibold flex-fill">
                        <i class="ph-circle-notch spinner fw-semibold"></i>
                        Lưu
                    </button>
                    <button wire:click="submit" wire:loading.remove class="shadow btn btn-primary fw-semibold flex-fill">
                        <i class="ph-floppy-disk fw-semibold"></i>
                        Lưu
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
