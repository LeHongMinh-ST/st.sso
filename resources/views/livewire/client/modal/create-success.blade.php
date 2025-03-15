<div id="model-success" wire:ignore.self class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo mới ứng dụng thành công</h5>
            </div>

            <div class="modal-body">
                <p>
                    <b>ID:</b> {{ $clientId }}
                    <span onclick="copyToClipboard('{{ $clientId }}')" style="cursor: pointer;" title="Copy">
                        <i class="fas fa-copy"></i>
                    </span>
                </p>
                <p>
                    <b>Secret:</b> {{ $clientSecret }}
                    <span onclick="copyToClipboard('{{ $clientSecret }}')" style="cursor: pointer;" title="Copy">
                        <i class="ph-copy"></i>
                    </span>
                </p>
                <p class="text-warning">Lưu ý: Mã bí mật chỉ hiển thị một lần!</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" wire:click="closeSuccessModal()">Đóng</button>
            </div>
        </div>
    </div>
</div>


@script
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Copied to clipboard: ' + text);
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
@endscript
