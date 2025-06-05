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

            <!-- Import Progress Display -->
            @if($isImporting)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-info-subtle border-info">
                            <div class="card-body">
                                <h6 class="card-title text-info">
                                    <i class="ph-circle-notch spinner me-2"></i>
                                    Đang nhập dữ liệu...
                                </h6>
                                
                                <!-- Progress Bar -->
                                <div class="progress mb-3" style="height: 20px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                                         role="progressbar" 
                                         style="width: {{ $importProgress }}%"
                                         aria-valuenow="{{ $importProgress }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ $importProgress }}%
                                    </div>
                                </div>
                                
                                <!-- Status Text -->
                                <p class="mb-2 text-info">{{ $importStatus }}</p>
                                
                                <!-- Import Stats -->
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="text-success">
                                            <i class="ph-check-circle"></i>
                                            <strong>{{ $importedCount }}</strong>
                                            <br><small>Thành công</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-danger">
                                            <i class="ph-x-circle"></i>
                                            <strong>{{ $errorCount }}</strong>
                                            <br><small>Lỗi</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-info">
                                            <i class="ph-clock"></i>
                                            <strong>{{ $importProgress }}%</strong>
                                            <br><small>Hoàn thành</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form wire:submit="import">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="file" class="form-label">Chọn file Excel</label>
                            <input wire:model="file" 
                                   type="file" 
                                   class="form-control @error('file') is-invalid @enderror" 
                                   id="file" 
                                   accept=".xlsx,.xls,.csv"
                                   @if($isImporting) disabled @endif>
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
                        <button wire:loading.attr="disabled" 
                                wire:target="import" 
                                type="submit" 
                                class="btn btn-success fw-semibold"
                                @if($isImporting) disabled @endif>
                            <span wire:loading.remove wire:target="import">
                                @if($isImporting)
                                    <i class="ph-circle-notch spinner me-1"></i> Đang xử lý...
                                @else
                                    <i class="ph-microsoft-excel-logo me-1"></i> Nhập danh sách sinh viên
                                @endif
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

    <!-- Auto-close and Echo listener script -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Auto close form
            Livewire.on('autoCloseImportForm', () => {
                setTimeout(() => {
                    @this.closeImportForm();
                }, 3000);
            });

            // Listen for import progress updates via Echo
            if (typeof Echo !== 'undefined') {
                Echo.private('import-progress.{{ auth()->id() }}')
                    .listen('ImportProgress', (data) => {
                        @this.updateProgress(data);
                    });
            } else {
                // Fallback: Use polling when Echo is not available
                let pollInterval;
                
                // Start polling when import begins
                Livewire.on('importStarted', () => {
                    pollInterval = setInterval(() => {
                        @this.checkImportProgress();
                    }, 2000); // Poll every 2 seconds
                });
                
                // Stop polling when import completes
                Livewire.on('importCompleted', () => {
                    if (pollInterval) {
                        clearInterval(pollInterval);
                    }
                });
            }
        });
    </script>
</div>
