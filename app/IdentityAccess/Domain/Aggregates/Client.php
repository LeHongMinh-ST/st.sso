<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Aggregates;

use App\IdentityAccess\Domain\Events\ClientWasRegistered;
use App\IdentityAccess\Domain\ValueObjects\ClientId;
use App\IdentityAccess\Domain\ValueObjects\ClientSecret;
use App\SharedKernel\Domain\Exceptions\InvalidArgumentException;

/**
 * Client aggregate root.
 * Represents OAuth2 client application.
 * Manages client registration and configuration.
 *
 * SECURITY: Client secret is handled securely and never exposed in events.
 */
final class Client
{
    private array $domainEvents = [];

    private ClientId $id;
    private string $name;
    private ?string $description;
    private ?string $thumbnail;
    private string $redirectUri;
    private ClientSecret $secret;
    private bool $isRevoked;
    private bool $isPersonalAccessClient;
    private bool $isPasswordClient;
    /** @var array<string> Allowed roles for this client */
    private array $allowedRoles;
    /** @var array<string> Grant types supported */
    private array $grantTypes;
    /** @var array<string> Scopes supported */
    private array $scopes;
    private ?string $provider;

    /**
     * Private constructor to enforce immutability.
     *
     * @param ClientId $id
     * @param string $name
     * @param string $redirectUri
     * @param ClientSecret $secret
     * @param bool $isRevoked
     * @param bool $isPersonalAccessClient
     * @param bool $isPasswordClient
     * @param array<string> $allowedRoles
     * @param array<string> $grantTypes
     * @param array<string> $scopes
     * @param string|null $description
     * @param string|null $thumbnail
     * @param string|null $provider
     */
    private function __construct(
        ClientId $id,
        string $name,
        string $redirectUri,
        ClientSecret $secret,
        bool $isRevoked = false,
        bool $isPersonalAccessClient = false,
        bool $isPasswordClient = false,
        array $allowedRoles = [],
        array $grantTypes = [],
        array $scopes = [],
        ?string $description = null,
        ?string $thumbnail = null,
        ?string $provider = null,
    ) {
        $this->id = $id;
        $this->name = trim($name);
        $this->redirectUri = $this->validateRedirectUri($redirectUri);
        $this->secret = $secret;
        $this->isRevoked = $isRevoked;
        $this->isPersonalAccessClient = $isPersonalAccessClient;
        $this->isPasswordClient = $isPasswordClient;
        $this->allowedRoles = $allowedRoles;
        $this->grantTypes = $grantTypes;
        $this->scopes = $scopes;
        $this->description = $description ? trim($description) : null;
        $this->thumbnail = $thumbnail;
        $this->provider = $provider;
    }

    /**
     * Factory method to register a new OAuth2 client.
     *
     * @param ClientId $id Client ID
     * @param string $name Client name
     * @param string $redirectUri Redirect URI
     * @param ClientSecret $secret Client secret
     * @param bool $isPersonalAccessClient Whether this is a personal access client
     * @param bool $isPasswordClient Whether this is a password client
     * @param array<string> $allowedRoles Allowed roles for this client
     * @param array<string> $grantTypes Grant types supported
     * @param array<string> $scopes Scopes supported
     * @param string|null $description Client description
     * @param string|null $thumbnail Client thumbnail URL
     * @param string|null $provider Provider name
     * @return self
     */
    public static function register(
        ClientId $id,
        string $name,
        string $redirectUri,
        ClientSecret $secret,
        bool $isPersonalAccessClient = false,
        bool $isPasswordClient = false,
        array $allowedRoles = [],
        array $grantTypes = [],
        array $scopes = [],
        ?string $description = null,
        ?string $thumbnail = null,
        ?string $provider = null,
    ): self {
        $client = new self(
            $id,
            $name,
            $redirectUri,
            $secret,
            false, // Not revoked when created
            $isPersonalAccessClient,
            $isPasswordClient,
            $allowedRoles,
            $grantTypes,
            $scopes,
            $description,
            $thumbnail,
            $provider,
        );

        $client->recordEvent(new ClientWasRegistered(
            $id->toString(),
            $name,
            $redirectUri,
        ));

        return $client;
    }

    /**
     * Reconstruct Client from persistence (without triggering events).
     * Used when loading from database.
     *
     * @param ClientId $id
     * @param string $name
     * @param string $redirectUri
     * @param ClientSecret $secret
     * @param bool $isRevoked
     * @param bool $isPersonalAccessClient
     * @param bool $isPasswordClient
     * @param array<string> $allowedRoles
     * @param array<string> $grantTypes
     * @param array<string> $scopes
     * @param string|null $description
     * @param string|null $thumbnail
     * @param string|null $provider
     * @return self
     */
    public static function fromPersistence(
        ClientId $id,
        string $name,
        string $redirectUri,
        ClientSecret $secret,
        bool $isRevoked = false,
        bool $isPersonalAccessClient = false,
        bool $isPasswordClient = false,
        array $allowedRoles = [],
        array $grantTypes = [],
        array $scopes = [],
        ?string $description = null,
        ?string $thumbnail = null,
        ?string $provider = null,
    ): self {
        // Create without triggering events - this is reconstruction from persistence
        return new self(
            $id,
            $name,
            $redirectUri,
            $secret,
            $isRevoked,
            $isPersonalAccessClient,
            $isPasswordClient,
            $allowedRoles,
            $grantTypes,
            $scopes,
            $description,
            $thumbnail,
            $provider,
        );
    }

    /**
     * Revoke client.
     *
     * @return void
     */
    public function revoke(): void
    {
        $this->isRevoked = true;
    }

    /**
     * Unrevoke client.
     *
     * @return void
     */
    public function unrevoke(): void
    {
        $this->isRevoked = false;
    }

    /**
     * Update redirect URI.
     *
     * @param string $redirectUri New redirect URI
     * @return void
     */
    public function updateRedirectUri(string $redirectUri): void
    {
        $this->redirectUri = $this->validateRedirectUri($redirectUri);
    }

    /**
     * Get client ID.
     *
     * @return ClientId
     */
    public function id(): ClientId
    {
        return $this->id;
    }

    /**
     * Get client name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get client description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * Get client thumbnail URL.
     *
     * @return string|null
     */
    public function thumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * Get redirect URI.
     *
     * @return string
     */
    public function redirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * Get client secret.
     *
     * SECURITY: Returns ClientSecret value object, handle with care.
     *
     * @return ClientSecret
     */
    public function secret(): ClientSecret
    {
        return $this->secret;
    }

    /**
     * Check if client is revoked.
     *
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->isRevoked;
    }

    /**
     * Check if client is personal access client.
     *
     * @return bool
     */
    public function isPersonalAccessClient(): bool
    {
        return $this->isPersonalAccessClient;
    }

    /**
     * Check if client is password client.
     *
     * @return bool
     */
    public function isPasswordClient(): bool
    {
        return $this->isPasswordClient;
    }

    /**
     * Get allowed roles.
     *
     * @return array<string>
     */
    public function allowedRoles(): array
    {
        return $this->allowedRoles;
    }

    /**
     * Get grant types.
     *
     * @return array<string>
     */
    public function grantTypes(): array
    {
        return $this->grantTypes;
    }

    /**
     * Get scopes.
     *
     * @return array<string>
     */
    public function scopes(): array
    {
        return $this->scopes;
    }

    /**
     * Get provider.
     *
     * @return string|null
     */
    public function provider(): ?string
    {
        return $this->provider;
    }

    /**
     * Pull and clear domain events.
     *
     * @return array<object>
     */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    /**
     * Validate redirect URI format.
     *
     * @param string $redirectUri
     * @return string Validated redirect URI
     * @throws InvalidArgumentException
     */
    private function validateRedirectUri(string $redirectUri): string
    {
        $trimmed = trim($redirectUri);

        if (empty($trimmed)) {
            throw new InvalidArgumentException('Redirect URI cannot be empty');
        }

        // Basic URL validation
        if (!filter_var($trimmed, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid redirect URI format');
        }

        // Security: Only allow http/https schemes
        $scheme = parse_url($trimmed, PHP_URL_SCHEME);
        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidArgumentException('Redirect URI must use http or https scheme');
        }

        return $trimmed;
    }

    /**
     * Record a domain event.
     *
     * @param object $event Domain event
     * @return void
     */
    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}
