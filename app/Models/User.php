<?php

namespace App\Models;

use App\Enums\Role;
use App\Enums\Status;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'user_name',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'code',
        'department_id',
        'faculty_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'role' => Role::class,
        'status' => Status::class,
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }
}
