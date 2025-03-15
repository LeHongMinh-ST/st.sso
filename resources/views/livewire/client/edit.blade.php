<div class="row">
    <div class="col-md-9 col-12">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bold">
                        <i class="ph-package"></i>
                        Thông tin ứng dụng
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <label for="name" class="col-form-label">
                                    Tên <span class="required">*</span>
                                </label>
                                <input wire:model.live="name" type="text" id="name" class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="redirect" class="col-form-label">
                                    Redirect URL <span class="required">*</span>
                                </label>
                                <input wire:model.live="redirect" type="text" id="redirect" class="form-control @error('redirect') is-invalid @enderror">
                                @error('redirect')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="description" class="col-form-label">
                                    Mô tả
                                </label>
                                <textarea wire:model.live="description" id="description" class="form-control @error('description') is-invalid @enderror"></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header bold">
                        <i class="ph-user"></i>
                        Đối tượng
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach (\App\Enums\Role::getDescription() as $key => $role)
                                <div class="col-12">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" wire:model.live="allowed_roles"
                                            value="{{ $key }}"
                                            @if ($key === \App\Enums\Role::SuperAdmin->value) disabled checked @endif
                                            id="role_{{ $key }}">
                                        <label class="form-check-label" for="role_{{ $key }}">{{ $role }}</label>
                                    </div>
                                </div>
                            @endforeach

                        </div>

                    </div>
                </div>
            </div>
        </div>
       


    </div>
    <div class="col-md-3 col-12">
        <div class="card">
            <div class="card-header bold">
                <i class="ph-gear-six"></i>
                Hành động
            </div>
            <div class="gap-2 card-body d-flex justify-content-center">
                <button wire:loading wire:target="submit" class="btn btn-primary flex-fill">
                    <i class="ph-circle-notch spinner"></i>
                    Lưu
                </button>
                <button wire:click="submit" wire:loading.remove class="btn btn-primary flex-fill">
                    <i class="ph-floppy-disk"></i>
                    Lưu
                </button>
                <a href="{{ route('client.show', $client->id) }}" type="button" class="btn btn-warning flex-fill"><i class="ph-arrow-counter-clockwise"></i> Trở lại</a>
            </div>
        </div>
    </div>
</div>
