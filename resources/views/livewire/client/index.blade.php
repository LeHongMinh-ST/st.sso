<div>
    <div class="card">
        <div class="py-3 card-header d-flex justify-content-between">
            <div class="gap-2 d-flex">
                <div>
                    <input wire:model.live="search" type="text" class="form-control" placeholder="Tìm kiếm...">
                </div>
            </div>
            <div class="gap-2 d-flex">
                <div>
                    <a href="{{ route('client.create') }}" type="button" class="px-2 btn btn-primary btn-icon">
                        <i class="px-1 ph-plus-circle"></i><span>Thêm mới</span>
                    </a>
                </div>

            </div>
        </div>

        <div class="table-responsive-md">
            <table class="table fs-table">
                <thead>
                    <tr class="table-light">
                        <th>STT</th>
                        <th>Ứng dụng</th>
                        <th>Đường dẫn truy cập</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $item)
                        <tr>
                            <td class="text-center" width="5%">{{ $loop->index + 1 + $clients->perPage() * ($clients->currentPage() - 1) }}</td>
                            <td width="35%">
                                <a href="{{ route('client.show', $item->id) }}">
                                    <img
                                         src="{{ Avatar::create($item->name)->setShape('square')->toBase64() }}"
                                         class="w-32px h-32px" alt="">
                                    {{ $item->name }}
                                </a>
                            </td>
                            <td width="35%"> <a href="{{ $item->baseRedirectUrl }}" taget="_blank"><i class="ph-link-simple"></i> {{ $item->baseRedirectUrl }}</a></td>
                            <td width="15%">{{ $item->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <x-table-empty :colspan="4" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $clients->links('vendor.pagination.theme') }}
</div>
