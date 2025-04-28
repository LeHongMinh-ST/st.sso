<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 */
class PermissionGroup extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'group_code', 'code');
    }
}
