<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Eloquent;

use App\OrganizationalStructure\Infrastructure\Eloquent\Faculty;
use App\OrganizationalStructure\Infrastructure\Eloquent\User;

use App\SharedKernel\Domain\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property Status $status
 * @property-read \App\OrganizationalStructure\Infrastructure\Eloquent\Faculty|null $faculty
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\OrganizationalStructure\Infrastructure\Eloquent\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Department extends Model
{
    protected $fillable = [
        'name',
        'status',
        'faculty_id',
    ];

    protected $casts = [
        'status' => Status::class,
    ];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query;
    }
}
