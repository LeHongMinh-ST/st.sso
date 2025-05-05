<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Role;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

/**
 *
 *
 * @property int $id
 * @property string $user_name
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string|null $phone
 * @property Role $role
 * @property Status $status
 * @property string $code
 * @property bool $is_change_password
 * @property int|null $department_id
 * @property int|null $faculty_id
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Client> $clients
 * @property-read int|null $clients_count
 * @property-read Department|null $department
 * @property-read Faculty|null $faculty
 * @property-read string $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Token> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User faculty($facultyIds)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User search($search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFacultyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsChangePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUserName($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
        'is_change_password',
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

    protected $casts = [
        'role' => Role::class,
        'status' => Status::class,
    ];

    protected $appends = [
        'full_name',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->last_name} {$this->first_name}";
    }

    public function isSuperAdmin(): bool
    {
        return Role::SuperAdmin === $this->role;
    }

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class, 'user_roles');
    }

    /**
     * Check if the user has the given role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if the user has the given permission.
     */
    public function hasPermissionTo(string $permissionCode): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permissionCode)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(string $roleName): void
    {
        $role = \App\Models\Role::where('name', $roleName)->first();

        if ($role) {
            // Check if the user already has this role
            if (!$this->hasRole($roleName)) {
                $this->roles()->attach($role->id);
            }
        }
    }

    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }
        return $query->where(DB::raw("CONCAT(last_name, ' ', first_name)"), 'like', "%{$search}%")
            ->orWhere('user_name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
    }

    public function scopeFaculty($query, $facultyId)
    {
        $hasNull = "0" === $facultyId;

        return $query->when(!empty($facultyId), fn ($q) => $q->where('faculty_id', $facultyId))
            ->when($hasNull, fn ($q) => $q->orWhereNull('faculty_id'));
    }

    public function scopeRole($query, $roles)
    {
        if (empty($roles)) {
            return $query;
        }

        return $query->where('role', $roles);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_change_password' => 'boolean',
        ];
    }
}
