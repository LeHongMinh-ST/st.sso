<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for UserIdentity.
 * Maps to users table (shares table with OrganizationalStructure User).
 *
 * Note: UserIdentity và OrganizationalStructure User share the same table (users).
 * UserIdentity handles authentication fields: user_name, password, is_change_password, is_only_login_ms.
 */
class UserIdentity extends Model
{
    /**
     * Table name (shared with OrganizationalStructure User).
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'user_name',
        'email',
        'password',
        'is_change_password',
        'is_only_login_ms',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_change_password' => 'boolean',
        'is_only_login_ms' => 'boolean',
        'email_verified_at' => 'datetime',
    ];
}
