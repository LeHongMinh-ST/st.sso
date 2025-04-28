<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Permission Groups
    |--------------------------------------------------------------------------
    |
    | This is the list of permission groups in the application.
    | Each group has a name, code, and description.
    |
    */
    'groups' => [
        [
            'name' => 'Quản lý người dùng',
            'code' => 'user',
            'description' => 'Các quyền liên quan đến quản lý người dùng',
        ],
        [
            'name' => 'Quản lý khoa',
            'code' => 'faculty',
            'description' => 'Các quyền liên quan đến quản lý khoa',
        ],
        [
            'name' => 'Quản lý bộ môn',
            'code' => 'department',
            'description' => 'Các quyền liên quan đến quản lý bộ môn',
        ],
        [
            'name' => 'Quản lý ứng dụng',
            'code' => 'client',
            'description' => 'Các quyền liên quan đến quản lý ứng dụng',
        ],
        [
            'name' => 'Quản lý vai trò',
            'code' => 'role',
            'description' => 'Các quyền liên quan đến quản lý vai trò',
        ],
        [
            'name' => 'API',
            'code' => 'api',
            'description' => 'Các quyền liên quan đến API',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | This is the list of permissions in the application.
    | Each permission has a name, code, group_code, and description.
    |
    */
    'permissions' => [
        // User permissions
        [
            'name' => 'Xem danh sách người dùng',
            'code' => 'user.view',
            'group_code' => 'user',
            'description' => 'Cho phép xem danh sách người dùng',
        ],
        [
            'name' => 'Tạo người dùng mới',
            'code' => 'user.create',
            'group_code' => 'user',
            'description' => 'Cho phép tạo người dùng mới',
        ],
        [
            'name' => 'Chỉnh sửa người dùng',
            'code' => 'user.edit',
            'group_code' => 'user',
            'description' => 'Cho phép chỉnh sửa thông tin người dùng',
        ],
        [
            'name' => 'Xóa người dùng',
            'code' => 'user.delete',
            'group_code' => 'user',
            'description' => 'Cho phép xóa người dùng',
        ],
        [
            'name' => 'Đặt lại mật khẩu',
            'code' => 'user.reset_password',
            'group_code' => 'user',
            'description' => 'Cho phép đặt lại mật khẩu người dùng',
        ],

        // Faculty permissions
        [
            'name' => 'Xem danh sách khoa',
            'code' => 'faculty.view',
            'group_code' => 'faculty',
            'description' => 'Cho phép xem danh sách khoa',
        ],
        [
            'name' => 'Tạo khoa mới',
            'code' => 'faculty.create',
            'group_code' => 'faculty',
            'description' => 'Cho phép tạo khoa mới',
        ],
        [
            'name' => 'Chỉnh sửa khoa',
            'code' => 'faculty.edit',
            'group_code' => 'faculty',
            'description' => 'Cho phép chỉnh sửa thông tin khoa',
        ],
        [
            'name' => 'Xóa khoa',
            'code' => 'faculty.delete',
            'group_code' => 'faculty',
            'description' => 'Cho phép xóa khoa',
        ],

        // Department permissions
        [
            'name' => 'Xem danh sách bộ môn',
            'code' => 'department.view',
            'group_code' => 'department',
            'description' => 'Cho phép xem danh sách bộ môn',
        ],
        [
            'name' => 'Tạo bộ môn mới',
            'code' => 'department.create',
            'group_code' => 'department',
            'description' => 'Cho phép tạo bộ môn mới',
        ],
        [
            'name' => 'Chỉnh sửa bộ môn',
            'code' => 'department.edit',
            'group_code' => 'department',
            'description' => 'Cho phép chỉnh sửa thông tin bộ môn',
        ],
        [
            'name' => 'Xóa bộ môn',
            'code' => 'department.delete',
            'group_code' => 'department',
            'description' => 'Cho phép xóa bộ môn',
        ],

        // Client permissions
        [
            'name' => 'Xem danh sách ứng dụng',
            'code' => 'client.view',
            'group_code' => 'client',
            'description' => 'Cho phép xem danh sách ứng dụng',
        ],
        [
            'name' => 'Tạo ứng dụng mới',
            'code' => 'client.create',
            'group_code' => 'client',
            'description' => 'Cho phép tạo ứng dụng mới',
        ],
        [
            'name' => 'Chỉnh sửa ứng dụng',
            'code' => 'client.edit',
            'group_code' => 'client',
            'description' => 'Cho phép chỉnh sửa thông tin ứng dụng',
        ],
        [
            'name' => 'Xóa ứng dụng',
            'code' => 'client.delete',
            'group_code' => 'client',
            'description' => 'Cho phép xóa ứng dụng',
        ],

        // Role permissions
        [
            'name' => 'Xem danh sách vai trò',
            'code' => 'role.view',
            'group_code' => 'role',
            'description' => 'Cho phép xem danh sách vai trò',
        ],
        [
            'name' => 'Tạo vai trò mới',
            'code' => 'role.create',
            'group_code' => 'role',
            'description' => 'Cho phép tạo vai trò mới',
        ],
        [
            'name' => 'Chỉnh sửa vai trò',
            'code' => 'role.edit',
            'group_code' => 'role',
            'description' => 'Cho phép chỉnh sửa thông tin vai trò',
        ],
        [
            'name' => 'Xóa vai trò',
            'code' => 'role.delete',
            'group_code' => 'role',
            'description' => 'Cho phép xóa vai trò',
        ],

        // API permissions
        [
            'name' => 'API Người dùng',
            'code' => 'api.user',
            'group_code' => 'api',
            'description' => 'Cho phép truy cập API người dùng',
        ],
        [
            'name' => 'API Khoa',
            'code' => 'api.faculty',
            'group_code' => 'api',
            'description' => 'Cho phép truy cập API khoa',
        ],
        [
            'name' => 'API Ứng dụng',
            'code' => 'api.client',
            'group_code' => 'api',
            'description' => 'Cho phép truy cập API ứng dụng',
        ],
        [
            'name' => 'API Vai trò',
            'code' => 'api.role',
            'group_code' => 'api',
            'description' => 'Cho phép truy cập API vai trò',
        ],
        [
            'name' => 'API Xác thực',
            'code' => 'api.auth',
            'group_code' => 'api',
            'description' => 'Cho phép truy cập API xác thực',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    |
    | This is the list of roles in the application.
    | Each role has a name, code, and description.
    |
    */
    'roles' => [
        [
            'name' => 'Quản trị viên',
            'code' => 'super_admin',
            'description' => 'Có tất cả các quyền trong hệ thống',
        ],
        [
            'name' => 'Quản trị viên cấp cao',
            'code' => 'admin',
            'description' => 'Có quyền quản lý người dùng, khoa, ứng dụng và vai trò',
        ],
        [
            'name' => 'Quản trị viên khoa',
            'code' => 'faculty_admin',
            'description' => 'Có quyền quản lý người dùng và xem thông tin khoa',
        ],
        [
            'name' => 'Giảng viên',
            'code' => 'teacher',
            'description' => 'Có quyền xem thông tin người dùng và khoa',
        ],
        [
            'name' => 'Sinh viên',
            'code' => 'student',
            'description' => 'Có quyền xem thông tin khoa',
        ],
        [
            'name' => 'Người dùng cơ bản',
            'code' => 'normal',
            'description' => 'Có quyền cơ bản',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Permissions
    |--------------------------------------------------------------------------
    |
    | This is the mapping of roles to permissions.
    | Each role has a list of permission codes.
    |
    */
    'role_permissions' => [
        'super_admin' => [
            // All permissions
            'user.view', 'user.create', 'user.edit', 'user.delete', 'user.reset_password',
            'faculty.view', 'faculty.create', 'faculty.edit', 'faculty.delete',
            'department.view', 'department.create', 'department.edit', 'department.delete',
            'client.view', 'client.create', 'client.edit', 'client.delete',
            'role.view', 'role.create', 'role.edit', 'role.delete',
            'api.user', 'api.faculty', 'api.client', 'api.role', 'api.auth',
        ],
        'admin' => [
            'user.view', 'user.create', 'user.edit', 'user.reset_password',
            'faculty.view', 'faculty.create', 'faculty.edit',
            'department.view', 'department.create', 'department.edit',
            'client.view',
            'role.view',
            'api.user', 'api.faculty', 'api.auth',
        ],
        'faculty_admin' => [
            'user.view', 'user.create', 'user.edit', 'user.reset_password',
            'faculty.view',
            'department.view',
            'api.user', 'api.faculty', 'api.auth',
        ],
        'teacher' => [
            'user.view',
            'faculty.view',
            'department.view',
        ],
        'student' => [
            'faculty.view',
            'department.view',
        ],
        'normal' => [
            // No permissions
        ],
    ],
];
