<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    // Không sử dụng LogsActivity trait để tránh vòng lặp vô hạn

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'before',
        'after',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
    ];

    /**
     * Lấy người dùng thực hiện hành động
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy model liên quan đến hành động
     */
    public function subject(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * Lọc theo người dùng
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Lọc theo loại hành động
     */
    public function scopeOfAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Lọc theo loại model
     */
    public function scopeOnModel($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            // Giới hạn kích thước dữ liệu JSON để tránh lỗi bộ nhớ
            if (is_array($model->before) && json_encode($model->before) && mb_strlen(json_encode($model->before)) > 65000) {
                $model->before = ['message' => 'Data too large to store', 'size' => mb_strlen(json_encode($model->before))];
            }

            if (is_array($model->after) && json_encode($model->after) && mb_strlen(json_encode($model->after)) > 65000) {
                $model->after = ['message' => 'Data too large to store', 'size' => mb_strlen(json_encode($model->after))];
            }
        });
    }
}
