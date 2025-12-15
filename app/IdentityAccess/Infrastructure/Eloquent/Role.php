<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Eloquent model for Role.
 */
class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'display_name',
        'description',
    ];

    /**
     * The permissions that belong to the role.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    /**
     * The users that belong to the role.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\OrganizationalStructure\Infrastructure\Eloquent\User::class, 'user_roles', 'role_id', 'user_id');
    }
}
