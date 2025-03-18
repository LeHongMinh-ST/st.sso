<div>
    <div class="card">
        <div class="py-3 card-header d-flex justify-content-between">
            <div class="gap-2 row">
                <div>
                    <input wire:model.live="search" type="text" class="form-control" placeholder="Tìm kiếm...">
                </div>
                {{-- <div class="col-4"> --}}
                {{--    <select wire:model.live="facultyIds" class="form-select"> --}}
                {{--        <option value="">Tất cả</option> --}}
                {{--        @foreach ($faculties as $faculty) --}}
                {{--            <option value="{{ $faculty->id }}">{{ $faculty->name }}</option> --}}
                {{--        @endforeach --}}
                {{--    </select> --}}
                {{-- </div> --}}
                {{-- <div class="col-4"> --}}
                {{--    <select wire:model.live="roles" class="form-select"> --}}
                {{--        <option value="">Tất cả</option> --}}
                {{--        @foreach (\App\Enums\Role::getDescription() as $role => $description) --}}
                {{--            <option value="{{ $role }}">{{ $description }}</option> --}}
                {{--        @endforeach --}}
                {{--    </select> --}}
                {{-- </div> --}}
            </div>
            <div class="gap-2 d-flex">
                <div>
                    <a href="{{ route('user.create') }}" type="button" class="px-2 shadow btn btn-primary btn-icon fw-semibold">
                        <i class="px-1 ph-plus-circle fw-semibold"></i><span>Thêm mới</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="table-responsive-md">
            <table class="table fs-table">
                <thead>
                    <tr class="table-light">
                        <th>STT</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Loại người dùng</th>
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
