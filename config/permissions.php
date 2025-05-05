<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Permission Groups
    |--------------------------------------------------------------------------
    |
    | This is the list of permission groups in the application.
    | Each group has a unique code and a display name.
    |
    */
    'groups' => [
        [
            'name' => 'Người dùng',
            'code' => 'user',
            'permissions' => [
                [
                    'name' => 'Xem danh sách người dùng',
                    'code' => 'user.view',
                ],
                [
                    'name' => 'Thêm người dùng',
                    'code' => 'user.create',
                ],
                [
                    'name' => 'Sửa người dùng',
                    'code' => 'user.edit',
                ],
                [
                    'name' => 'Xóa người dùng',
                    'code' => 'user.delete',
                ],
                [
                    'name' => 'Đặt lại mật khẩu người dùng',
                    'code' => 'user.reset_password',
                ],
            ],
        ],
        [
            'name' => 'Khoa',
            'code' => 'faculty',
            'permissions' => [
                [
                    'name' => 'Xem danh sách khoa',
                    'code' => 'faculty.view',
                ],
                [
                    'name' => 'Thêm khoa',
                    'code' => 'faculty.create',
                ],
                [
                    'name' => 'Sửa khoa',
                    'code' => 'faculty.edit',
                ],
                [
                    'name' => 'Xóa khoa',
                    'code' => 'faculty.delete',
                ],
            ],
        ],
        [
            'name' => 'Bộ môn',
            'code' => 'department',
            'permissions' => [
                [
                    'name' => 'Xem danh sách bộ môn',
                    'code' => 'department.view',
                ],
                [
                    'name' => 'Thêm bộ môn',
                    'code' => 'department.create',
                ],
                [
                    'name' => 'Sửa bộ môn',
                    'code' => 'department.edit',
                ],
                [
                    'name' => 'Xóa bộ môn',
                    'code' => 'department.delete',
                ],
            ],
        ],
        [
            'name' => 'Ứng dụng',
            'code' => 'client',
            'permissions' => [
                [
                    'name' => 'Xem danh sách ứng dụng',
                    'code' => 'client.view',
                ],
                [
                    'name' => 'Thêm ứng dụng',
                    'code' => 'client.create',
                ],
                [
                    'name' => 'Sửa ứng dụng',
                    'code' => 'client.edit',
                ],
                [
                    'name' => 'Xóa ứng dụng',
                    'code' => 'client.delete',
                ],
            ],
        ],
        [
            'name' => 'Vai trò',
            'code' => 'role',
            'permissions' => [
                [
                    'name' => 'Xem danh sách vai trò',
                    'code' => 'role.view',
                ],
                [
                    'name' => 'Thêm vai trò',
                    'code' => 'role.create',
                ],
                [
                    'name' => 'Sửa vai trò',
                    'code' => 'role.edit',
                ],
                [
                    'name' => 'Xóa vai trò',
                    'code' => 'role.delete',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Roles
    |--------------------------------------------------------------------------
    |
    | This is the list of default roles in the application.
    | Each role has a unique name and a display name.
    |
    */
    'roles' => [
        [
            'name' => 'super-admin',
            'display_name' => 'Quản trị viên cấp cao',
            'description' => 'Có tất cả các quyền trong hệ thống',
        ],
        [
            'name' => 'admin',
            'display_name' => 'Quản trị viên',
            'description' => 'Có quyền quản lý người dùng, khoa, ứng dụng và vai trò',
        ],
        [
            'name' => 'faculty-admin',
            'display_name' => 'Quản trị viên khoa',
            'description' => 'Có quyền quản lý người dùng và xem thông tin khoa',
        ],
        [
            'name' => 'teacher',
            'display_name' => 'Giảng viên',
            'description' => 'Có quyền xem thông tin người dùng và khoa',
        ],
        [
            'name' => 'student',
            'display_name' => 'Sinh viên',
            'description' => 'Có quyền xem thông tin khoa',
        ],
        [
            'name' => 'normal',
            'display_name' => 'Người dùng thông thường',
            'description' => 'Không có quyền đặc biệt',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Permissions
    |--------------------------------------------------------------------------
    |
    | This is the list of permissions for each role.
    | Each role has a list of permission codes.
    |
    */
    'role_permissions' => [
        'super-admin' => [
            'user.view', 'user.create', 'user.edit', 'user.delete', 'user.reset_password',
            'faculty.view', 'faculty.create', 'faculty.edit', 'faculty.delete',
            'department.view', 'department.create', 'department.edit', 'department.delete',
            'client.view', 'client.create', 'client.edit', 'client.delete',
            'role.view', 'role.create', 'role.edit', 'role.delete',
        ],
        'admin' => [
            'user.view', 'user.create', 'user.edit', 'user.reset_password',
            'faculty.view', 'faculty.create', 'faculty.edit',
            'department.view', 'department.create', 'department.edit',
            'client.view',
            'role.view',
        ],
        'faculty-admin' => [
            'user.view', 'user.create', 'user.edit', 'user.reset_password',
            'faculty.view',
            'department.view',
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
        'normal' => [],
    ],
];
