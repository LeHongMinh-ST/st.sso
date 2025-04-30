<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Role as RoleEnum;
use App\Enums\Status;
use App\Traits\LogsActivity;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property RoleEnum $role
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
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use LogsActivity;

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
        'role' => RoleEnum::class,
        'status' => Status::class,
    ];

    protected $appends = [
        'full_name',
        'name',
        'display_name',
        'filament_user_name',
        'avatar_url',
    ];

    /**
     * Convert the model to its string representation.
     */
    public function __toString(): string
    {
        return $this->getFullNameAttribute() ?: $this->user_name ?: $this->email ?: 'User';
    }

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

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        // Sử dụng Gravatar nếu không có avatar được lưu trữ
        return 'https://www.gravatar.com/avatar/' . md5(mb_strtolower(trim($this->email))) . '?d=mp';
    }

    public function getNameAttribute(): string
    {
        return $this->getFullNameAttribute() ?: $this->user_name ?: $this->email ?: 'User';
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->getFullNameAttribute() ?: $this->user_name ?: $this->email ?: 'User';
    }

    public function getEmail(): string
    {
        return $this->email ?: '';
    }

    public function getEmailAttribute(): string
    {
        return $this->attributes['email'] ?: '';
    }

    public function getUserNameAttribute(): string
    {
        return $this->attributes['user_name'] ?: '';
    }

    public function getFirstNameAttribute(): string
    {
        return $this->attributes['first_name'] ?: '';
    }

    public function getLastNameAttribute(): string
    {
        return $this->attributes['last_name'] ?: '';
    }

    public function getFilamentUserNameAttribute(): string
    {
        return $this->getFullNameAttribute() ?: $this->user_name ?: $this->email ?: 'User';
    }

    public function isSuperAdmin(): bool
    {
        return RoleEnum::SuperAdmin === $this->role;
    }

    /**
     * Get the roles that belong to the user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Check if the user has the given role
     */
    public function hasRole(string $roleCode): bool
    {
        return $this->roles()->where('code', $roleCode)->exists();
    }

    /**
     * Check if the user has any of the given roles
     */
    public function hasAnyRole(array $roleCodes): bool
    {
        return $this->roles()->whereIn('code', $roleCodes)->exists();
    }

    /**
     * Check if the user has all of the given roles
     */
    public function hasAllRoles(array $roleCodes): bool
    {
        return $this->roles()->whereIn('code', $roleCodes)->count() === count($roleCodes);
    }

    /**
     * Assign the given role to the user
     */
    public function assignRole(string $roleCode): void
    {
        $role = Role::where('code', $roleCode)->first();
        if ($role && !$this->hasRole($roleCode)) {
            $this->roles()->attach($role->id);
        }
    }

    /**
     * Remove the given role from the user
     */
    public function removeRole(string $roleCode): void
    {
        $role = Role::where('code', $roleCode)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    /**
     * Sync the given roles to the user
     */
    public function syncRoles(array $roleCodes): void
    {
        $roleIds = Role::whereIn('code', $roleCodes)->pluck('id')->toArray();
        $this->roles()->sync($roleIds);
    }

    /**
     * Check if the user has the given permission
     */
    public function hasPermission(string $permissionCode): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permissionCode)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user has any of the given permissions
     */
    public function hasAnyPermission(array $permissionCodes): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasAnyPermission($permissionCodes)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user has all of the given permissions
     */
    public function hasAllPermissions(array $permissionCodes): bool
    {
        $userPermissions = [];
        foreach ($this->roles as $role) {
            $rolePermissions = $role->permissions()->pluck('code')->toArray();
            $userPermissions = array_merge($userPermissions, $rolePermissions);
        }
        $userPermissions = array_unique($userPermissions);

        foreach ($permissionCodes as $permissionCode) {
            if (!in_array($permissionCode, $userPermissions)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Determine if the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // Tất cả người dùng đều có thể truy cập panel
    }

    /**
     * Get the name that should be used when displaying this user in Filament.
     */
    public function getFilamentName(): string
    {
        return $this->getFullNameAttribute() ?: $this->user_name ?: $this->email ?: 'User';
    }

    /**
     * Get the user's name.
     */
    public function getUserName(): string
    {
        return $this->getFullNameAttribute() ?: $this->user_name ?: $this->email ?: 'User';
    }

    /**
     * Get the user's name.
     */
    public function getName(): string
    {
        return $this->getFullNameAttribute() ?: $this->user_name ?: $this->email ?: 'User';
    }

    /**
     * Get the user's name.
     */
    public function name(): string
    {
        return $this->getFullNameAttribute() ?: $this->user_name ?: $this->email ?: 'User';
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword(): string
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken(): ?string
    {
        return $this->{$this->getRememberTokenName()};
    }

    /**
     * Set the token value for the "remember me" session.
     */
    public function setRememberToken($value): void
    {
        $this->{$this->getRememberTokenName()} = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName(): string
    {
        return 'remember_token';
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

    /**
     * Lấy nhật ký hoạt động của người dùng
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
