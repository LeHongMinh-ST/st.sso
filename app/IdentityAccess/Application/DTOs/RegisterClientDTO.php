<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\DTOs;

/**
 * Data Transfer Object for registering an OAuth2 client.
 */
final class RegisterClientDTO
{
    /**
     * @param string $name Client name
     * @param string $redirectUri Redirect URI
     * @param bool $isPersonalAccessClient Whether this is a personal access client
     * @param bool $isPasswordClient Whether this is a password client
     * @param array<string> $allowedRoles Allowed roles for this client
     * @param array<string> $grantTypes Grant types supported
     * @param array<string> $scopes Scopes supported
     * @param string|null $description Client description
     * @param string|null $thumbnail Client thumbnail URL
     * @param string|null $provider Provider name
     */
    public function __construct(
        public readonly string $name,
        public readonly string $redirectUri,
        public readonly bool $isPersonalAccessClient = false,
        public readonly bool $isPasswordClient = false,
        public readonly array $allowedRoles = [],
        public readonly array $grantTypes = [],
        public readonly array $scopes = [],
        public readonly ?string $description = null,
        public readonly ?string $thumbnail = null,
        public readonly ?string $provider = null,
    ) {
    }

    /**
     * Create DTO from array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            $data['redirect_uri'] ?? '',
            $data['is_personal_access_client'] ?? false,
            $data['is_password_client'] ?? false,
            $data['allowed_roles'] ?? [],
            $data['grant_types'] ?? [],
            $data['scopes'] ?? [],
            $data['description'] ?? null,
            $data['thumbnail'] ?? null,
            $data['provider'] ?? null,
        );
    }

    /**
     * Create DTO from Request.
     *
     * @param \Illuminate\Http\Request $request
     * @return self
     */
    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            $request->input('name', ''),
            $request->input('redirect_uri', ''),
            $request->boolean('is_personal_access_client', false),
            $request->boolean('is_password_client', false),
            $request->input('allowed_roles', []),
            $request->input('grant_types', []),
            $request->input('scopes', []),
            $request->input('description'),
            $request->input('thumbnail'),
            $request->input('provider'),
        );
    }

    /**
     * Convert DTO to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'redirect_uri' => $this->redirectUri,
            'is_personal_access_client' => $this->isPersonalAccessClient,
            'is_password_client' => $this->isPasswordClient,
            'allowed_roles' => $this->allowedRoles,
            'grant_types' => $this->grantTypes,
            'scopes' => $this->scopes,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail,
            'provider' => $this->provider,
        ];
    }
}
