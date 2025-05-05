<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermissionGroup extends Model
{
    protected $fillable = [
        'name',
        'code',
        'display_name',
        'description',
    ];

    /**
     * The permissions that belong to the group.
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
