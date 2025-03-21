<div>
    <div class="card">
        <div class="py-3 card-header d-flex justify-content-between">
            <div class="gap-2 d-flex">
                <div>
                    <a href="{{ route('faculty.edit', $faculty->id) }}" type="button" class="px-2 shadow btn fw-semibold btn-primary btn-icon">
                        <i class="px-1 ph-note-pencil fw-semibold"></i><span>Chỉnh sửa</span>
                    </a>
                </div>

                <div>
                    <button wire:click="openDeleteModal()" class="px-2 shadow btn btn-danger btn-icon fw-semibold">
                        <i class="px-1 ph-trash fw-semibold"></i><span>Xoá</span>
                    </button>
                </div>
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
    </script>
@endscript
