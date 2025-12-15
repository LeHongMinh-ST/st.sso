<?php

declare(strict_types=1);

namespace Tests\Unit\IdentityAccess\Domain\Aggregates;

use App\IdentityAccess\Domain\Aggregates\UserIdentity;
use App\IdentityAccess\Domain\Events\PasswordWasChanged;
use App\IdentityAccess\Domain\Events\UserIdentityWasCreated;
use App\IdentityAccess\Domain\Events\UserWasAuthenticated;
use App\IdentityAccess\Domain\ValueObjects\PasswordHash;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\IdentityAccess\Domain\ValueObjects\Username;
use App\SharedKernel\Domain\ValueObjects\Email;
use PHPUnit\Framework\TestCase;

final class UserIdentityTest extends TestCase
{
    /**
     * Test that create method creates user identity and records event.
     */
    public function test_create_user_identity_records_event(): void
    {
        $id = UserIdentityId::generate();
        $username = Username::fromString('testuser');
        $email = Email::fromString('test@example.com');
        $passwordHash = $this->createPasswordHash('password123');

        $userIdentity = UserIdentity::create(
            $id,
            'user-uuid-123',
            $username,
            $email,
            $passwordHash
        );

        $events = $userIdentity->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserIdentityWasCreated::class, $events[0]);
    }

    /**
     * Test that authenticate returns true for correct password.
     */
    public function test_authenticate_returns_true_for_correct_password(): void
    {
        $passwordHash = $this->createPasswordHash('password123');
        $userIdentity = $this->createUserIdentity($passwordHash);

        // Clear initial event
        $userIdentity->pullDomainEvents();

        $verifier = fn (string $p, string $h): bool => password_verify($p, $h);
        $result = $userIdentity->authenticate('password123', $verifier);

        $this->assertTrue($result);

        $events = $userIdentity->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserWasAuthenticated::class, $events[0]);
    }

    /**
     * Test that authenticate returns false for incorrect password.
     */
    public function test_authenticate_returns_false_for_incorrect_password(): void
    {
        $passwordHash = $this->createPasswordHash('password123');
        $userIdentity = $this->createUserIdentity($passwordHash);

        // Clear initial event
        $userIdentity->pullDomainEvents();

        $verifier = fn (string $p, string $h): bool => password_verify($p, $h);
        $result = $userIdentity->authenticate('wrongpassword', $verifier);

        $this->assertFalse($result);

        $events = $userIdentity->pullDomainEvents();
        $this->assertCount(0, $events); // No event for failed authentication
    }

    /**
     * Test that authenticate returns false if user is inactive.
     */
    public function test_authenticate_returns_false_if_user_inactive(): void
    {
        $passwordHash = $this->createPasswordHash('password123');
        $userIdentity = $this->createUserIdentity($passwordHash);
        $userIdentity->deactivate();

        $verifier = fn (string $p, string $h): bool => password_verify($p, $h);
        $result = $userIdentity->authenticate('password123', $verifier);

        $this->assertFalse($result);
    }

    /**
     * Test that authenticate returns false if Microsoft-only login.
     */
    public function test_authenticate_returns_false_if_microsoft_only_login(): void
    {
        $passwordHash = $this->createPasswordHash('password123');
        $userIdentity = $this->createUserIdentity($passwordHash);
        $userIdentity->enableMicrosoftOnlyLogin();

        $verifier = fn (string $p, string $h): bool => password_verify($p, $h);
        $result = $userIdentity->authenticate('password123', $verifier);

        $this->assertFalse($result);
    }

    /**
     * Test that changePassword updates password and clears mustChangePassword flag.
     */
    public function test_change_password_updates_password_and_clears_flag(): void
    {
        $passwordHash = $this->createPasswordHash('oldpassword');
        $userIdentity = $this->createUserIdentity($passwordHash);
        $userIdentity->requirePasswordChange();

        // Clear initial event
        $userIdentity->pullDomainEvents();

        $newPasswordHash = $this->createPasswordHash('newpassword');
        $userIdentity->changePassword($newPasswordHash);

        $this->assertFalse($userIdentity->mustChangePassword());

        $events = $userIdentity->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(PasswordWasChanged::class, $events[0]);
    }

    /**
     * Test that requirePasswordChange sets flag.
     */
    public function test_require_password_change_sets_flag(): void
    {
        $userIdentity = $this->createUserIdentity($this->createPasswordHash('password123'));

        $userIdentity->requirePasswordChange();

        $this->assertTrue($userIdentity->mustChangePassword());
    }

    /**
     * Test that enableMicrosoftOnlyLogin sets flag.
     */
    public function test_enable_microsoft_only_login_sets_flag(): void
    {
        $userIdentity = $this->createUserIdentity($this->createPasswordHash('password123'));

        $userIdentity->enableMicrosoftOnlyLogin();

        $this->assertTrue($userIdentity->isOnlyMicrosoftLogin());
    }

    /**
     * Test that disableMicrosoftOnlyLogin clears flag.
     */
    public function test_disable_microsoft_only_login_clears_flag(): void
    {
        $userIdentity = $this->createUserIdentity($this->createPasswordHash('password123'));
        $userIdentity->enableMicrosoftOnlyLogin();

        $userIdentity->disableMicrosoftOnlyLogin();

        $this->assertFalse($userIdentity->isOnlyMicrosoftLogin());
    }

    /**
     * Test that activate sets isActive to true.
     */
    public function test_activate_sets_is_active_to_true(): void
    {
        $userIdentity = $this->createUserIdentity($this->createPasswordHash('password123'));
        $userIdentity->deactivate();

        $userIdentity->activate();

        $this->assertTrue($userIdentity->isActive());
    }

    /**
     * Test that deactivate sets isActive to false.
     */
    public function test_deactivate_sets_is_active_to_false(): void
    {
        $userIdentity = $this->createUserIdentity($this->createPasswordHash('password123'));

        $userIdentity->deactivate();

        $this->assertFalse($userIdentity->isActive());
    }

    /**
     * Test that pullDomainEvents returns and clears events.
     */
    public function test_pull_domain_events_returns_and_clears_events(): void
    {
        $userIdentity = $this->createUserIdentity($this->createPasswordHash('password123'));

        $events1 = $userIdentity->pullDomainEvents();
        $this->assertCount(1, $events1); // UserIdentityWasCreated

        $events2 = $userIdentity->pullDomainEvents();
        $this->assertCount(0, $events2); // Events cleared
    }

    /**
     * Test that user identity is active by default when created.
     */
    public function test_user_identity_is_active_by_default(): void
    {
        $userIdentity = $this->createUserIdentity($this->createPasswordHash('password123'));

        $this->assertTrue($userIdentity->isActive());
    }

    /**
     * Helper method to create UserIdentity.
     */
    private function createUserIdentity(PasswordHash $passwordHash): UserIdentity
    {
        return UserIdentity::create(
            UserIdentityId::generate(),
            'user-uuid-123',
            Username::fromString('testuser'),
            Email::fromString('test@example.com'),
            $passwordHash
        );
    }

    /**
     * Helper method to create PasswordHash.
     */
    private function createPasswordHash(string $plainPassword): PasswordHash
    {
        $hasher = fn (string $p): string => password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]);

        return PasswordHash::fromPlainText($plainPassword, $hasher);
    }
}
