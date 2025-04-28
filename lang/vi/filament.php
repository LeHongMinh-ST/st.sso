<?php

declare(strict_types=1);

return [
    // Các action
    'actions' => [
        'edit' => 'Sửa',
        'view' => 'Xem',
        'create' => 'Tạo mới',
        'delete' => 'Xóa',
        'restore' => 'Khôi phục',
        'force_delete' => 'Xóa vĩnh viễn',
        'save' => 'Lưu',
        'cancel' => 'Hủy',
        'close' => 'Đóng',
        'back' => 'Quay lại',
        'submit' => 'Gửi',
        'filter' => 'Lọc',
        'reset' => 'Đặt lại',
        'search' => 'Tìm kiếm',
        'export' => 'Xuất',
        'import' => 'Nhập',
        'download' => 'Tải xuống',
        'upload' => 'Tải lên',
    ],

    // Các label chung
    'common' => [
        'record' => 'bản ghi',
        'records' => 'bản ghi',
        'result' => 'kết quả',
        'results' => 'kết quả',
        'all' => 'Tất cả',
        'none' => 'Không có',
        'yes' => 'Có',
        'no' => 'Không',
        'confirm' => 'Xác nhận',
        'loading' => 'Đang tải',
        'saving' => 'Đang lưu',
        'saved' => 'Đã lưu',
        'deleting' => 'Đang xóa',
        'deleted' => 'Đã xóa',
        'creating' => 'Đang tạo',
        'created' => 'Đã tạo',
        'updating' => 'Đang cập nhật',
        'updated' => 'Đã cập nhật',
        'restoring' => 'Đang khôi phục',
        'restored' => 'Đã khôi phục',
    ],

    // Các thông báo
    'messages' => [
        'created' => ':resource đã được tạo thành công.',
        'updated' => ':resource đã được cập nhật thành công.',
        'deleted' => ':resource đã được xóa thành công.',
        'restored' => ':resource đã được khôi phục thành công.',
        'force_deleted' => ':resource đã được xóa vĩnh viễn.',
        'saved' => 'Đã lưu thành công.',
        'delete_confirmation' => 'Bạn có chắc chắn muốn xóa :resource này?',
        'delete_confirmation_count' => 'Bạn có chắc chắn muốn xóa :count :resource?',
        'restore_confirmation' => 'Bạn có chắc chắn muốn khôi phục :resource này?',
        'restore_confirmation_count' => 'Bạn có chắc chắn muốn khôi phục :count :resource?',
        'force_delete_confirmation' => 'Bạn có chắc chắn muốn xóa vĩnh viễn :resource này?',
        'force_delete_confirmation_count' => 'Bạn có chắc chắn muốn xóa vĩnh viễn :count :resource?',
        'no_records' => 'Không có :resource nào.',
        'no_results' => 'Không tìm thấy kết quả nào.',
        'search_results' => 'Tìm thấy :count kết quả.',
    ],

    // Các tiêu đề
    'titles' => [
        'list' => 'Danh sách :resource',
        'create' => 'Tạo :resource',
        'edit' => 'Sửa :resource',
        'view' => 'Xem :resource',
    ],

    // Các nhãn form
    'forms' => [
        'select_placeholder' => 'Chọn một tùy chọn',
        'multi_select_placeholder' => 'Chọn các tùy chọn',
        'search_placeholder' => 'Tìm kiếm',
        'date_placeholder' => 'Chọn ngày',
        'time_placeholder' => 'Chọn giờ',
        'datetime_placeholder' => 'Chọn ngày và giờ',
    ],

    // Các nhãn bảng
    'tables' => [
        'empty' => 'Không có dữ liệu nào.',
        'filter' => 'Lọc',
        'reset_filter' => 'Đặt lại bộ lọc',
        'search' => 'Tìm kiếm',
        'actions' => 'Hành động',
        'bulk_actions' => 'Hành động hàng loạt',
        'select_all' => 'Chọn tất cả',
        'select_page' => 'Chọn trang',
        'per_page' => 'Mỗi trang',
        'previous' => 'Trước',
        'next' => 'Sau',
        'showing' => 'Hiển thị từ :first đến :last trong tổng số :total bản ghi',
    ],

    // Các nhãn điều hướng
    'navigation' => [
        'dashboard' => 'Bảng điều khiển',
        'settings' => 'Cài đặt',
        'profile' => 'Hồ sơ',
        'logout' => 'Đăng xuất',
    ],
];
