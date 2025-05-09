<div>
    <div class="card">
        <div class="py-3 card-header d-flex justify-content-between">
            <div class="gap-2 d-flex">
                <div>
                    <input wire:model.live.debounce.500ms="search" type="text" class="form-control" placeholder="Tìm kiếm...">
                </div>
            </div>
            <div class="gap-2 d-flex">
                @can('create', App\Models\Client::class)
                <div>
                    <a href="{{ route('client.create') }}" type="button" class="px-2 shadow btn btn-primary btn-icon fw-semibold">
                        <i class="px-1 ph-plus-circle fw-semibold"></i><span>Thêm mới</span>
                    </a>
                </div>
                @endcan
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
                        <th>Ứng dụng</th>
                        <th>Client ID</th>
                        <th>Đường dẫn truy cập</th>
                        <th>Hiển thị trang chủ</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $item)
                        <tr>
                            <td class="text-center" width="5%">{{ $loop->index + 1 + $clients->perPage() * ($clients->currentPage() - 1) }}</td>
                            <td width="30%">
                                <a class="fw-semibold" href="{{ route('client.show', $item->id) }}">
                                    <img src="{{ Avatar::create($item->name)->setShape('square')->toBase64() }}" class="w-32px h-32px" alt="">
                                    {{ $item->name }}
                                </a>
                            </td>
                            <td>
                                {{ $item->id }}

                                <a href="javascript:void(0)" class="copy-link text-dark"
                                   data-id="{{ $item->id }}">
                                    <i class="ph-copy"></i>
                                </a>
                            </td>

                            <td width="15%">
                                <a href="{{ $item->baseRedirectUrl }}" target="_blank">
                                    <i class="ph-link-simple"></i> {{ $item->baseRedirectUrl }}
                                </a>
                            </td>
                            <td>
                                <div class="mb-2 form-check form-switch">
                                    <input wire:click="toggleShowDashboard('{{ $item->id }}')"type="checkbox" class="form-check-input" @if ($item->is_show_dashboard) checked @endif>
                                </div>
                            </td>
                            <td width="15%">{{ $item->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <x-table-empty :colspan="6" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $clients->links('vendor.pagination.theme') }}
</div>

@script
    <script>
        $('.copy-link').on('click', function() {
            const copy = $(this).attr('data-id');

            const $tempInput = $('<input>');

            $('body').append($tempInput);

            $tempInput.val(copy).select();

            document.execCommand('copy');

            $tempInput.remove();
        });
    </script>
@endscript
