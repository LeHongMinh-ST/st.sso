<div>
    <div class="card">
        <div class="py-3 card-header">
            <div class="d-flex justify-content-between">

                <div class="flex-wrap gap-2 d-flex">
                    <div>
                        <input wire:model.live.debounce.500ms="search" type="text" class="form-control" placeholder="Tìm kiếm...">
                    </div>
                    <div>

                        <button class="shadow btn btn-outline-primary fw-semibold btn-icon" type="button" data-bs-toggle="collapse" wire:click="toogleFilter()">
                            <i class="ph-funnel fw-semibold"></i>
                            Bộ lọc
                        </button>
                    </div>
                </div>
                <div class="gap-2 d-flex">
                    <div>
                        <a href="{{ route('user.create') }}" type="button" class="px-2 shadow btn btn-primary btn-icon fw-semibold">
                            <i class="px-1 ph-plus-circle fw-semibold"></i><span>Thêm mới</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="collapse @if ($isShowFilter) show @endif" id="filter-collapsed">
                <div class="mt-3 row">
                    <div class="col-md-4 col-12">
                        <label class="form-label">Khoa</label>
                        <select wire:model.live="facultyId" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="0">Không có</option>
                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label">Loại người dùng</label>
                        <select wire:model.live="role" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach (\App\Enums\Role::getDescription() as $role => $description)
                                <option value="{{ $role }}">{{ $description }}</option>
                            @endforeach
                        </select>
                    </div>
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
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Loại tài khoản</th>
                        <th>Khoa</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $item)
                        <tr>
                            <td class="text-center" width="5%">{{ $loop->index + 1 + $users->perPage() * ($users->currentPage() - 1) }}</td>
                            <td width="30%">
                                <a class="fw-semibold" href="{{ route('user.show', $item->id) }}">
                                    <img src="{{ Avatar::create($item->fullName)->toBase64() }}" class="w-32px h-32px" alt="">
                                    {{ $item->fullName }}
                                </a>
                            </td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->phone }}</td>
                            <td>
                                <x-role-badge :role="$item->role" />
                            </td>
                            <td>{{ $item->faculty->name ?? 'Không có' }}</td>
                            <td>
                                <x-status-badge :status="$item->status" />
                            </td>
                            <td width="10%">{{ $item->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <x-table-empty :colspan="8" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $users->links('vendor.pagination.theme') }}
</div>
