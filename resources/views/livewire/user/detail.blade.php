<div>
    <div class="card">
        <div class="py-3 card-header d-flex justify-content-between">
            <div class="gap-2 d-flex">
                <div>
                    <a href="{{ route('user.edit', $user->id) }}" type="button" class="px-2 btn btn-primary btn-icon">
                        <i class="px-1 ph-note-pencil"></i><span>Chỉnh sửa</span>
                    </a>
                </div>

                @if ($user->id != Auth::user()->id)
                    <div>
                        <button wire:click="openDeleteModal()" class="px-2 btn btn-danger btn-icon">
                            <i class="px-1 ph-trash"></i><span>Xoá</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-12">
                    <p>
                        <b>Tên đăng nhập:</b> {{ $user->user_name }}
                    </p>
                    <p>
                        <b>Email:</b> {{ $user->email }}
                    </p>
                    <p>
                        <b>Số điện thoại:</b> {{ $user->phone }}
                    </p>
                    <p>
                        <b>Khoa:</b> {{ $user->faculty->name ?? 'Không có' }}
                    </p>

                    <p>
                        <b>Ngày tạo:</b> {{ $user->created_at->format('d/m/Y') }}
                    </p>
                </div>
                <div class="col-md-6 col-12">
                    <p>
                        <b>Họ và tên:</b> {{ $user->fullName }}
                    </p>
                    <p>
                        <b>Loại người dùng:</b> <x-role-badge :role="$user->role" />
                    </p>
                    <p>
                        <b>Trạng thái:</b> <x-status-badge :status="$user->status" />
                    </p>
                </div>
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
                    Livewire.dispatch('deleteUser')
                }
            })
        })
    </script>
@endscript
