<div>
    <div class="card">
        <div class="py-3 card-header d-flex justify-content-between">
            <h5 class="mb-0"><i class="ph-shield me-1"></i> Thông tin vai trò</h5>
            <div class="gap-2 d-flex">
                @can('role.edit')
                <a href="{{ route('role.edit', $role->id) }}" type="button" class="px-2 shadow btn fw-semibold btn-primary btn-icon">
                    <i class="px-1 ph-note-pencil fw-semibold"></i><span>Chỉnh sửa</span>
                </a>
                @endcan
                @if($role->name !== 'super-admin')
                    @can('role.delete')
                    <button wire:click="openDeleteModal()" class="px-2 shadow btn btn-danger btn-icon fw-semibold">
                        <i class="px-1 ph-trash fw-semibold"></i><span>Xoá</span>
                    </button>
                    @endcan
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-12">
                    <p>
                        <b>Tên vai trò:</b> {{ $role->name }}
                    </p>
                </div>
                <div class="col-md-6 col-12">
                    <p>
                        <b>Ngày tạo:</b> {{ $role->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0"><i class="ph-key me-1"></i> Danh sách quyền</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($permissions as $group => $items)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-capitalize">{{ $group }}</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    @foreach($items as $permission)
                                        <li class="mb-2">
                                            <i class="ph-check-circle text-success me-1"></i>
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
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            Vai trò này chưa được gán quyền nào.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@script
    <script>
        window.addEventListener('onOpenDeleteModal', () => {
            new swal({
                title: "Bạn có chắc chắn?",
                text: "Dữ liệu sau khi xóa không thể phục hồi!",
                showCancelButton: true,
                confirmButtonColor: "#EE4444",
                confirmButtonText: "Đồng ý!",
                cancelButtonText: "Đóng!"
            }).then((value) => {
                if (value.isConfirmed) {
                    Livewire.dispatch('deleteRole')
                }
            })
        })
    </script>
@endscript
