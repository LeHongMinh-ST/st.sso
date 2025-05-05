<div>
    <div class="card">
        <div class="card-header bold d-flex align-items-center">
            <i class="ph-microsoft-excel-logo me-2 fs-3"></i>
            <span>Nhập danh sách sinh viên từ Excel - {{ $faculty->name }}</span>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12">
                    <p class="text-muted">Tải lên file Excel chứa danh sách sinh viên. File Excel phải có các cột sau:</p>
                    <ul class="text-muted">
                        <li><strong>ho</strong>: Họ sinh viên</li>
                        <li><strong>ten</strong>: Tên sinh viên</li>
                        <li><strong>email</strong>: Email sinh viên</li>
                        <li><strong>ma_sinh_vien</strong>: Mã sinh viên</li>
                        <li><strong>so_dien_thoai</strong>: Số điện thoại (không bắt buộc)</li>
                    </ul>
                    <p class="text-muted">Lưu ý: Dòng đầu tiên của file Excel phải là tên cột.</p>
                    <p>
                        <a href="{{ asset('templates/mau_import_sinh_vien.xlsx') }}" class="btn btn-sm btn-light" download>
                            <i class="ph-download"></i> Tải xuống file mẫu
                        </a>
                    </p>
                </div>
            </div>

            <form wire:submit="import">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="file" class="form-label">Chọn file Excel</label>
                            <input wire:model="file" type="file" class="form-control @error('file') is-invalid @enderror" id="file" accept=".xlsx,.xls,.csv">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button wire:click="closeImportForm" type="button" class="btn btn-light">
                            <i class="ph-x"></i> Hủy
                        </button>
                        <button wire:loading.attr="disabled" wire:target="import" type="submit" class="btn btn-success fw-semibold">
                            <span wire:loading.remove wire:target="import">
                                <i class="ph-microsoft-excel-logo me-1"></i> Nhập danh sách sinh viên
                            </span>
                            <span wire:loading wire:target="import">
                                <i class="ph-circle-notch spinner me-1"></i> Đang xử lý...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
