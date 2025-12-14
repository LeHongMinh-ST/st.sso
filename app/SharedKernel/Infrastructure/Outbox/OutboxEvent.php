<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Outbox;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for outbox events table.
 * This model is used only in Infrastructure layer for persistence.
 */
final class OutboxEvent extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'outbox_events';

    protected $fillable = [
        'id',
        'aggregate_type',
        'aggregate_id',
        'event_type',
        'payload',
        'created_at',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * Check if event is processed.
     *
     * @return bool
     */
    public function isProcessed(): bool
    {
        return null !== $this->processed_at;
    }

    /**
     * Mark event as processed.
     *
     * @return void
     */
    public function markAsProcessed(): void
    {
        $this->processed_at = now();
        $this->save();
    }
}
