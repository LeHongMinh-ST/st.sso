<div>
    <div class="card mb-3">
        <div class="py-3 card-header d-flex justify-content-between">
            <h5 class="mb-0"><i class="ph-buildings me-1"></i> Thông tin khoa</h5>
            <div class="gap-2 d-flex">
                @can('update', $faculty)
                <a href="{{ route('faculty.edit', $faculty->id) }}" type="button" class="px-2 shadow btn fw-semibold btn-primary btn-icon">
                    <i class="px-1 ph-note-pencil fw-semibold"></i><span>Chỉnh sửa</span>
                </a>
                @endcan
                @can('delete', $faculty)
                <button wire:click="openDeleteModal()" class="px-2 shadow btn btn-danger btn-icon fw-semibold">
                    <i class="px-1 ph-trash fw-semibold"></i><span>Xoá</span>
                </button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-12">
                    <p>
                        <b>Tên:</b> {{ $faculty->name }}
                    </p>
                </div>

                <div class="col-md-6 col-12">
                    <p>
                        <b>Trạng thái:</b> <x-status-badge :status="$faculty->status" />
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($showCreateUserForm && auth()->user()->can('create', App\Models\User::class))
        <div class="mb-3">
            <livewire:faculty.create-user :faculty="$faculty" />
        </div>
    @endif

    <div class="card">
        <div class="py-3 card-header">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0"><i class="ph-users me-1"></i> Danh sách người dùng trong khoa</h5>
                @can('create', App\Models\User::class)
                <button wire:click="toggleCreateUserForm()" class="btn btn-primary btn-icon">
                    <i class="ph-{{ $showCreateUserForm ? 'minus' : 'user-plus' }}"></i>
                </button>
                @endcan
            </div>
            <div class="gap-2 d-flex">
                <div>
                    <input wire:model.live.debounce.500ms="search" type="text" class="form-control" placeholder="Tìm kiếm người dùng...">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <div wire:loading class="my-3 text-center w-100">
                <span class="spinner-border spinner-border-sm"></span> Đang tải dữ liệu...
            </div>
            <table class="table fs-table" wire:loading.remove>
                <thead>
                    <tr class="table-light">
                        <th>STT</th>
                        <th>Họ và tên</th>
                        <th>Điện thoại</th>
                        <th>Loại tài khoản</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $item)
                        <tr>
                            <td class="text-center" width="5%">{{ $loop->index + 1 + $users->perPage() * ($users->currentPage() - 1) }}</td>
                            <td width="30%">
                                <a class="fw-semibold" href="{{ route('user.show', $item->id) }}">
                                    <div class="gap-2 d-flex align-items-center">
                                        <img src="{{ Avatar::create($item->fullName)->toBase64() }}" class="w-32px h-32px" alt="">
                                        <div class="flex-grow-1">
                                            <div>
                                                {{ $item->fullName }}
                                            </div>
                                            <div class="text-muted">
                                                {{ $item->email }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </td>
                            <td>{{ $item->phone }}</td>
                            <td>
                                <x-role-badge :role="$item->role" />
                            </td>
                            <td>
                                <x-status-badge :status="$item->status" />
                            </td>
                            <td width="10%">{{ $item->created_at->format('d/m/Y') }}</td>
                            <td width="10%">
                                <a href="{{ route('user.show', $item->id) }}" class="btn btn-sm btn-primary" title="Xem chi tiết">
                                    <i class="ph-eye"></i>
                                </a>
                                @can('resetPassword', $item)
                                <button class="btn btn-sm btn-warning reset-password-btn" data-id="{{ $item->id }}" title="Reset mật khẩu">
                                    <i class="ph-key"></i>
                                </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <x-table-empty :colspan="7" />
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $users->links('vendor.pagination.theme') }}
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
                    Livewire.dispatch('deleteFaculty')
                }
            })
        })

        // Xử lý sự kiện reset mật khẩu
        document.addEventListener('click', function(event) {
            if (event.target.closest('.reset-password-btn')) {
                const button = event.target.closest('.reset-password-btn');
                const userId = button.getAttribute('data-id');

                new swal({
                    title: "Reset mật khẩu?",
                    text: "Mật khẩu sẽ được đặt lại thành 'password' và người dùng sẽ được yêu cầu đổi mật khẩu khi đăng nhập lần sau!",
                    showCancelButton: true,
                    confirmButtonColor: "#FF9800",
                    confirmButtonText: "Đồng ý!",
                    cancelButtonText: "Đóng!"
                }).then((value) => {
                    if (value.isConfirmed) {
                        // Gọi API để reset mật khẩu
                        fetch(`/api/users/${userId}/reset-password`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                new swal({
                                    title: "Thành công!",
                                    text: "Mật khẩu đã được reset thành công!",
                                    icon: "success"
                                });
                            } else {
                                new swal({
                                    title: "Lỗi!",
                                    text: data.message || "Có lỗi xảy ra khi reset mật khẩu!",
                                    icon: "error"
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            new swal({
                                title: "Lỗi!",
                                text: "Có lỗi xảy ra khi reset mật khẩu!",
                                icon: "error"
                            });
                        });
                    }
                });
            }
        });
    </script>
@endscript
