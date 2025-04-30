<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ActivityLog;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Ghi nhật ký hoạt động
     *
     * @param string $action Hành động (create, update, delete, login, logout, etc.)
     * @param Model|null $model Model liên quan
     * @param array|null $before Dữ liệu trước khi thay đổi
     * @param array|null $after Dữ liệu sau khi thay đổi
     * @param string|null $description Mô tả hành động
     * @return int ID của bản ghi log đã tạo
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?array $before = null,
        ?array $after = null,
        ?string $description = null
    ): int {
        // Tránh vòng lặp vô hạn - không ghi log cho chính model ActivityLog
        if ($model instanceof ActivityLog) {
            throw new Exception('Cannot log ActivityLog model to prevent infinite recursion');
        }

        $user = Auth::user();

        // Giới hạn kích thước dữ liệu
        if ($before && is_array($before) && count($before) > 10) {
            $before = array_slice($before, 0, 10, true);
            $before['_truncated'] = 'Data was truncated due to size limitations';
        }

        if ($after && is_array($after) && count($after) > 10) {
            $after = array_slice($after, 0, 10, true);
            $after['_truncated'] = 'Data was truncated due to size limitations';
        }

        // Lọc các trường nhạy cảm trước khi lưu
        if ($before) {
            $before = self::filterSensitiveData($before);
        }

        if ($after) {
            $after = self::filterSensitiveData($after);
        }

        // Sử dụng DB::raw để tránh các sự kiện model
        $id = DB::transaction(fn () => DB::table('activity_logs')->insertGetId([
            'user_id' => $user?->id,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->getKey(),
            'before' => $before ? json_encode($before) : null,
            'after' => $after ? json_encode($after) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        // Trả về ID thay vì model để tránh vòng lặp vô hạn
        return $id;
    }

    /**
     * Ghi nhật ký đăng nhập
     *
     * @param \App\Models\User $user
     * @return int ID của bản ghi log đã tạo
     */
    public static function logLogin($user): int
    {
        return self::log(
            'login',
            $user,
            null,
            null,
            'Đăng nhập vào hệ thống'
        );
    }

    /**
     * Ghi nhật ký đăng xuất
     *
     * @return int ID của bản ghi log đã tạo
     */
    public static function logLogout(): int
    {
        return self::log(
            'logout',
            Auth::user(),
            null,
            null,
            'Đăng xuất khỏi hệ thống'
        );
    }

    /**
     * Ghi nhật ký tạo mới
     *
     * @param Model $model
     * @param array|null $attributes Dữ liệu của model đã tạo
     * @param string|null $description
     * @return int ID của bản ghi log đã tạo
     */
    public static function logCreated(Model $model, ?array $attributes = null, ?string $description = null): int
    {
        if (!$description) {
            $modelName = class_basename($model);
            $description = "Tạo mới {$modelName} #{$model->getKey()}";
        }

        return self::log(
            'create',
            $model,
            null,
            $attributes ?? [],
            $description
        );
    }

    /**
     * Ghi nhật ký cập nhật
     *
     * @param Model $model
     * @param array $oldAttributes
     * @param string|null $description
     * @return int ID của bản ghi log đã tạo
     */
    public static function logUpdated(Model $model, array $oldAttributes, ?string $description = null): int
    {
        if (!$description) {
            $modelName = class_basename($model);
            $description = "Cập nhật {$modelName} #{$model->getKey()}";
        }

        // Chỉ lấy các trường cơ bản để giảm kích thước dữ liệu
        $basicFields = ['id', 'name', 'title', 'email', 'description', 'status', 'updated_at'];
        $newAttributes = array_intersect_key($model->getAttributes(), array_flip($basicFields));

        return self::log(
            'update',
            $model,
            $oldAttributes,
            $newAttributes,
            $description
        );
    }

    /**
     * Ghi nhật ký xóa
     *
     * @param Model $model
     * @param array|null $attributes Dữ liệu của model trước khi xóa
     * @param string|null $description
     * @return int ID của bản ghi log đã tạo
     */
    public static function logDeleted(Model $model, ?array $attributes = null, ?string $description = null): int
    {
        if (!$description) {
            $modelName = class_basename($model);
            $description = "Xóa {$modelName} #{$model->getKey()}";
        }

        return self::log(
            'delete',
            $model,
            $attributes ?? $model->toArray(),
            null,
            $description
        );
    }

    /**
     * Lọc và giảm kích thước dữ liệu trước khi lưu
     *
     * @param array $data
     * @return array
     */
    protected static function filterSensitiveData(array $data): array
    {
        // Các trường nhạy cảm cần ẩn
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'secret',
            'token',
            'remember_token',
            'api_token',
        ];

        // Các trường cần loại bỏ để giảm kích thước
        $excludeFields = [
            'created_at',
            'updated_at',
            'deleted_at',
            'permissions',
            'roles',
            'media',
            'pivot',
        ];

        // Lọc dữ liệu
        $filteredData = [];
        foreach ($data as $key => $value) {
            // Bỏ qua các trường cần loại bỏ
            if (in_array($key, $excludeFields)) {
                continue;
            }

            // Ẩn các trường nhạy cảm
            if (in_array($key, $sensitiveFields)) {
                $filteredData[$key] = '******';
                continue;
            }

            // Giảm kích thước các mảng lớn
            if (is_array($value) && count($value) > 10) {
                $filteredData[$key] = '[Array with ' . count($value) . ' items]';
                continue;
            }

            // Giảm kích thước các chuỗi dài
            if (is_string($value) && mb_strlen($value) > 200) {
                $filteredData[$key] = mb_substr($value, 0, 200) . '... [truncated]';
                continue;
            }

            // Giữ nguyên các giá trị khác
            $filteredData[$key] = $value;
        }

        return $filteredData;
    }
}
