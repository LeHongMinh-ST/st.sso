<?php

declare(strict_types=1);

namespace App\IdentityAccess\Application\DTOs;

/**
 * Data Transfer Object for issuing an access token.
 */
final class IssueTokenDTO
{
    /**
     * @param string $userIdentityId User Identity ID (UUID)
     * @param string $clientId Client ID
     * @param array<string> $scopes Token scopes
     */
    public function __construct(
        public readonly string $userIdentityId,
        public readonly string $clientId,
        public readonly array $scopes = [],
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
            $data['user_identity_id'] ?? '',
            $data['client_id'] ?? '',
            $data['scopes'] ?? [],
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
            $request->input('user_identity_id', ''),
            $request->input('client_id', ''),
            $request->input('scopes', []),
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
            'user_identity_id' => $this->userIdentityId,
            'client_id' => $this->clientId,
            'scopes' => $this->scopes,
        ];
    }
}
