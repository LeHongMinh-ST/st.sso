<?php

declare(strict_types=1);

namespace App\IdentityAccess\Domain\Aggregates;

use App\IdentityAccess\Domain\Events\PasswordWasChanged;
use App\IdentityAccess\Domain\Events\UserIdentityWasCreated;
use App\IdentityAccess\Domain\Events\UserWasAuthenticated;
use App\IdentityAccess\Domain\ValueObjects\PasswordHash;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\IdentityAccess\Domain\ValueObjects\Username;
use App\SharedKernel\Domain\ValueObjects\Email;

/**
 * User Identity aggregate root.
 * Manages authentication credentials (not profile).
 * Links to UserId from OrganizationalStructure context.
 *
 * SECURITY: This aggregate never stores plain passwords, only hashes.
 */
final class UserIdentity
{
    private array $domainEvents = [];

    private UserIdentityId $id;
    private string $organizationalStructureUserId; // Link to OrganizationalStructure UserId
    private Username $username;
    private Email $email; // For login
    private PasswordHash $passwordHash;
    private bool $mustChangePassword;
    private bool $isOnlyMicrosoftLogin;
    private bool $isActive;

    /**
     * Private constructor to enforce immutability.
     *
     * @param UserIdentityId $id
     * @param string $organizationalStructureUserId
     * @param Username $username
     * @param Email $email
     * @param PasswordHash $passwordHash
     * @param bool $mustChangePassword
     * @param bool $isOnlyMicrosoftLogin
     * @param bool $isActive
     */
    private function __construct(
        UserIdentityId $id,
        string $organizationalStructureUserId,
        Username $username,
        Email $email,
        PasswordHash $passwordHash,
        bool $mustChangePassword = false,
        bool $isOnlyMicrosoftLogin = false,
        bool $isActive = true,
    ) {
        $this->id = $id;
        $this->organizationalStructureUserId = $organizationalStructureUserId;
        $this->username = $username;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->mustChangePassword = $mustChangePassword;
        $this->isOnlyMicrosoftLogin = $isOnlyMicrosoftLogin;
        $this->isActive = $isActive;
    }

    /**
     * Factory method to create a new user identity.
     *
     * @param UserIdentityId $id User Identity ID
     * @param string $organizationalStructureUserId User ID from OrganizationalStructure context
     * @param Username $username Username for authentication
     * @param Email $email Email for authentication
     * @param PasswordHash $passwordHash Hashed password (never plain text)
     * @param bool $mustChangePassword Whether user must change password on first login
     * @param bool $isOnlyMicrosoftLogin Whether user can only login via Microsoft
     * @return self
     */
    public static function create(
        UserIdentityId $id,
        string $organizationalStructureUserId,
        Username $username,
        Email $email,
        PasswordHash $passwordHash,
        bool $mustChangePassword = false,
        bool $isOnlyMicrosoftLogin = false,
    ): self {
        $userIdentity = new self(
            $id,
            $organizationalStructureUserId,
            $username,
            $email,
            $passwordHash,
            $mustChangePassword,
            $isOnlyMicrosoftLogin,
            true, // Always active when created
        );

        $userIdentity->recordEvent(new UserIdentityWasCreated(
            $id->toString(),
            $organizationalStructureUserId,
            (string) $email,
        ));

        return $userIdentity;
    }

    /**
     * Reconstruct UserIdentity from persistence (without triggering events).
     * Used when loading from database.
     *
     * @param UserIdentityId $id
     * @param string $organizationalStructureUserId
     * @param Username $username
     * @param Email $email
     * @param PasswordHash $passwordHash
     * @param bool $mustChangePassword
     * @param bool $isOnlyMicrosoftLogin
     * @param bool $isActive
     * @return self
     */
    public static function fromPersistence(
        UserIdentityId $id,
        string $organizationalStructureUserId,
        Username $username,
        Email $email,
        PasswordHash $passwordHash,
        bool $mustChangePassword = false,
        bool $isOnlyMicrosoftLogin = false,
        bool $isActive = true,
    ): self {
        // Create without triggering events - this is reconstruction from persistence
        return new self(
            $id,
            $organizationalStructureUserId,
            $username,
            $email,
            $passwordHash,
            $mustChangePassword,
            $isOnlyMicrosoftLogin,
            $isActive,
        );
    }

    /**
     * Authenticate user with password.
     * Returns true if authentication successful.
     *
     * SECURITY: Uses timing-safe password verification.
     *
     * @param string $plainPassword Plain text password to verify
     * @param callable $passwordVerifier Password verifier function (e.g., Hash::check)
     * @return bool True if authentication successful, false otherwise
     */
    public function authenticate(string $plainPassword, callable $passwordVerifier): bool
    {
        if (!$this->isActive) {
            return false;
        }

        if ($this->isOnlyMicrosoftLogin) {
            return false; // Cannot authenticate with password
        }

        $isValid = $this->passwordHash->verify($plainPassword, $passwordVerifier);

        if ($isValid) {
            $this->recordEvent(new UserWasAuthenticated(
                $this->id->toString(),
                $this->organizationalStructureUserId,
                (string) $this->email,
            ));
        }

        return $isValid;
    }

    /**
     * Change password.
     *
     * SECURITY: Only accepts PasswordHash (never plain password).
     *
     * @param PasswordHash $newPasswordHash New hashed password
     * @return void
     */
    public function changePassword(PasswordHash $newPasswordHash): void
    {
        $this->passwordHash = $newPasswordHash;
        $this->mustChangePassword = false;

        $this->recordEvent(new PasswordWasChanged($this->id->toString()));
    }

    /**
     * Mark password as must be changed.
     *
     * @return void
     */
    public function requirePasswordChange(): void
    {
        $this->mustChangePassword = true;
    }

    /**
     * Enable Microsoft-only login.
     *
     * @return void
     */
    public function enableMicrosoftOnlyLogin(): void
    {
        $this->isOnlyMicrosoftLogin = true;
    }

    /**
     * Disable Microsoft-only login.
     *
     * @return void
     */
    public function disableMicrosoftOnlyLogin(): void
    {
        $this->isOnlyMicrosoftLogin = false;
    }

    /**
     * Activate user identity.
     *
     * @return void
     */
    public function activate(): void
    {
        $this->isActive = true;
    }

    /**
     * Deactivate user identity.
     *
     * @return void
     */
    public function deactivate(): void
    {
        $this->isActive = false;
    }

    /**
     * Get user identity ID.
     *
     * @return UserIdentityId
     */
    public function id(): UserIdentityId
    {
        return $this->id;
    }

    /**
     * Get organizational structure user ID.
     *
     * @return string
     */
    public function organizationalStructureUserId(): string
    {
        return $this->organizationalStructureUserId;
    }

    /**
     * Get username.
     *
     * @return Username
     */
    public function username(): Username
    {
        return $this->username;
    }

    /**
     * Get email.
     *
     * @return Email
     */
    public function email(): Email
    {
        return $this->email;
    }

    /**
     * Get password hash.
     *
     * SECURITY: Returns PasswordHash value object, never plain password.
     *
     * @return PasswordHash
     */
    public function passwordHash(): PasswordHash
    {
        return $this->passwordHash;
    }

    /**
     * Check if password must be changed.
     *
     * @return bool
     */
    public function mustChangePassword(): bool
    {
        return $this->mustChangePassword;
    }

    /**
     * Check if user can only login via Microsoft.
     *
     * @return bool
     */
    public function isOnlyMicrosoftLogin(): bool
    {
        return $this->isOnlyMicrosoftLogin;
    }

    /**
     * Check if user identity is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
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
