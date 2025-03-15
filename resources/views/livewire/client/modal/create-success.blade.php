<div id="model-success" wire:ignore.self class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo mới ứng dụng thành công</h5>
            </div>

            <div class="modal-body">
                <p>
                    <b>ID:</b> {{ $clientId }}
                    <a href="javascript:void(0)" class="copy-link text-dark"
                       data-id="{{ $clientId }}">
                        <i class="ph-copy"></i>
                    </a>
                </p>
                <p>
                    <b>Secret:</b> {{ $clientSecret }}
                    <a href="javascript:void(0)" class="copy-link text-dark"
                       data-id="{{ $clientSecret }}">
                        <i class="ph-copy"></i>
                    </a>
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
