<?php

declare(strict_types=1);

namespace App\Models;

use Laravel\Passport\Client as PassportClient;

/**
 *
 *
 * @property string $id
 * @property int|null $user_id
 * @property string $name
 * @property string|null $thumbnail
 * @property string|null $description
 * @property string|null $secret
 * @property string|null $provider
 * @property string $redirect
 * @property bool $personal_access_client
 * @property bool $password_client
 * @property bool $revoked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\AuthCode> $authCodes
 * @property-read int|null $auth_codes_count
 * @property-read mixed $base_redirect_url
 * @property-read string|null $plain_secret
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Token> $tokens
 * @property-read int|null $tokens_count
 * @property-read User|null $user
 * @method static \Laravel\Passport\Database\Factories\ClientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client search($search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePasswordClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePersonalAccessClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereRedirect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereRevoked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUserId($value)
 * @mixin \Eloquent
 */
class Client extends PassportClient
{
    protected $casts = [
        'grant_types' => 'array',
        'scopes' => 'array',
        'personal_access_client' => 'bool',
        'password_client' => 'bool',
        'revoked' => 'bool',
        'allowed_roles' => 'array',
        'is_show_dashboard' => 'bool',
        'status' => '\App\Enums\Status',
    ];

    protected $fillable = [
        'name',
        'thumbnail',
        'description',
        'provider',
        'redirect',
        'personal_access_client',
        'password_client',
        'revoked',
        'allowed_roles',
        'grant_types',
        'scopes',
        'is_show_dashboard',
        'status',
        'logo',
    ];

    public function getBaseRedirectUrlAttribute()
    {
        if (!$this->redirect) {
            return null;
        }

        $scheme = parse_url($this->redirect, PHP_URL_SCHEME);
        $host = parse_url($this->redirect, PHP_URL_HOST);
        $port = parse_url($this->redirect, PHP_URL_PORT);

        $baseUrl = $scheme . '://' . $host;
        if ($port && 80 !== $port && 443 !== $port) {
            $baseUrl .= ':' . $port;
        }

        return $baseUrl;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query;
    }
}
