<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Log;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    public static function bootLogsActivity(): void
    {
        // Tránh vòng lặp đệ quy - không ghi log cho chính model ActivityLog
        if (ActivityLog::class === static::class) {
            return;
        }

        static::created(function (Model $model): void {
            try {
                // Kiểm tra xem model có phải là ActivityLog không
                if ($model instanceof ActivityLog) {
                    return;
                }

                // Giới hạn kích thước dữ liệu
                $attributes = array_intersect_key(
                    $model->getAttributes(),
                    array_flip(['id', 'name', 'title', 'email', 'description', 'created_at'])
                );

                ActivityLogService::logCreated($model, $attributes);
            } catch (Exception $e) {
                // Ghi log lỗi nhưng không làm ảnh hưởng đến luồng chính
                Log::error('Error logging activity: ' . $e->getMessage());
            }
        });

        static::updated(function (Model $model): void {
            try {
                // Kiểm tra xem model có phải là ActivityLog không
                if ($model instanceof ActivityLog) {
                    return;
                }

                // Chỉ lấy các trường đã thay đổi để giảm kích thước dữ liệu
                $oldAttributes = array_intersect_key(
                    $model->getOriginal(),
                    $model->getDirty()
                );

                // Chỉ giữ lại các trường cơ bản
                $basicFields = ['id', 'name', 'title', 'email', 'description', 'status', 'updated_at'];
                $oldAttributes = array_intersect_key($oldAttributes, array_flip($basicFields));

                // Chỉ ghi log nếu có thay đổi
                if (!empty($oldAttributes)) {
                    ActivityLogService::logUpdated($model, $oldAttributes);
                }
            } catch (Exception $e) {
                // Ghi log lỗi nhưng không làm ảnh hưởng đến luồng chính
                Log::error('Error logging activity: ' . $e->getMessage());
            }
        });

        static::deleted(function (Model $model): void {
            try {
                // Kiểm tra xem model có phải là ActivityLog không
                if ($model instanceof ActivityLog) {
                    return;
                }

                // Chỉ lấy các trường cơ bản để giảm kích thước dữ liệu
                $basicAttributes = array_intersect_key(
                    $model->getAttributes(),
                    array_flip(['id', 'name', 'title', 'email'])
                );

                ActivityLogService::logDeleted($model, $basicAttributes);
            } catch (Exception $e) {
                // Ghi log lỗi nhưng không làm ảnh hưởng đến luồng chính
                Log::error('Error logging activity: ' . $e->getMessage());
            }
        });
    }
}
