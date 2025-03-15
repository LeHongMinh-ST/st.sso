<div>
    <div class="card">
        <div class="py-3 card-header d-flex justify-content-between">
            <div class="gap-2 d-flex">
                <div>
                    <a href="{{ route('client.edit', $client->id) }}" type="button" class="px-2 btn btn-primary btn-icon">
                        <i class="px-1 ph-note-pencil"></i><span>Chỉnh sửa</span>
                    </a>
                </div>

                <div>
                    <button wire:click="openDeleteModal()" class="px-2 btn btn-danger btn-icon">
                        <i class="px-1 ph-trash"></i><span>Xoá</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <p>
                <b>Tên hiển thị:</b> {{ $client->name }}
            </p>
            <p>
                <b>Client ID:</b> {{ $client->id }}
                <a href="javascript:void(0)" class="copy-link text-dark"
                   data-id="{{ $client->id }}">
                    <i class="ph-copy"></i>
                </a>
            </p>
            <p>
                <b>Redirect Url:</b> {{ $client->redirect }}
            </p>
            <p>
                <b>Mô tả:</b> {{ $client->desciption }}
            </p>
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
                    Livewire.dispatch('deleteClient')
                }
            })
        })
    </script>
@endscript
