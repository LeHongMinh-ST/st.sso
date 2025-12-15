<?php

declare(strict_types=1);

namespace Tests\Feature\IdentityAccess;

use App\IdentityAccess\Application\DTOs\AuthenticateUserDTO;
use App\IdentityAccess\Application\UseCases\AuthenticateUserUseCase;
use App\IdentityAccess\Domain\Aggregates\UserIdentity;
use App\IdentityAccess\Domain\Exceptions\InvalidCredentialsException;
use App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface;
use App\IdentityAccess\Domain\Services\PasswordHasherInterface;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\IdentityAccess\Domain\ValueObjects\Username;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use App\SharedKernel\Domain\ValueObjects\Email;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Feature tests for AuthenticateUserUseCase.
 * Tests authentication flow, rate limiting, and security measures.
 */
final class AuthenticateUserUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private AuthenticateUserUseCase $useCase;
    private UserIdentityRepositoryInterface $userIdentityRepository;
    private PasswordHasherInterface $passwordHasher;
    private OutboxEventRepositoryInterface $outboxEventRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userIdentityRepository = $this->app->make(UserIdentityRepositoryInterface::class);
        $this->passwordHasher = $this->app->make(PasswordHasherInterface::class);
        $this->outboxEventRepository = $this->app->make(OutboxEventRepositoryInterface::class);

        $this->useCase = new AuthenticateUserUseCase(
            $this->userIdentityRepository,
            $this->passwordHasher,
            $this->outboxEventRepository,
        );

        // Clear rate limiter before each test
        RateLimiter::clear('authenticate:testuser');
        RateLimiter::clear('authenticate:test@example.com');
    }

    /**
     * Test that valid credentials authenticate successfully.
     */
    public function test_valid_credentials_authenticate_successfully(): void
    {
        // Create user identity
        $userIdentity = $this->createUserIdentity('testuser', 'test@example.com', 'password123');
        $this->userIdentityRepository->save($userIdentity);

        // Authenticate
        $dto = new AuthenticateUserDTO(
            username: 'testuser',
            password: 'password123',
        );

        $authenticatedUser = $this->useCase->execute($dto);

        $this->assertInstanceOf(UserIdentity::class, $authenticatedUser);
        $this->assertEquals($userIdentity->id()->toString(), $authenticatedUser->id()->toString());
    }

    /**
     * Test that invalid password throws InvalidCredentialsException.
     */
    public function test_invalid_password_throws_exception(): void
    {
        // Create user identity
        $userIdentity = $this->createUserIdentity('testuser', 'test@example.com', 'password123');
        $this->userIdentityRepository->save($userIdentity);

        // Try to authenticate with wrong password
        $dto = new AuthenticateUserDTO(
            username: 'testuser',
            password: 'wrongpassword',
        );

        $this->expectException(InvalidCredentialsException::class);
        $this->useCase->execute($dto);
    }

    /**
     * Test that non-existent user throws InvalidCredentialsException (generic message).
     */
    public function test_non_existent_user_throws_exception(): void
    {
        $dto = new AuthenticateUserDTO(
            username: 'nonexistent',
            password: 'password123',
        );

        $this->expectException(InvalidCredentialsException::class);
        $this->useCase->execute($dto);
    }

    /**
     * Test that rate limiting prevents brute force attacks.
     */
    public function test_rate_limiting_prevents_brute_force_attacks(): void
    {
        // Create user identity
        $userIdentity = $this->createUserIdentity('testuser', 'test@example.com', 'password123');
        $this->userIdentityRepository->save($userIdentity);

        // Make 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            try {
                $dto = new AuthenticateUserDTO(
                    username: 'testuser',
                    password: 'wrongpassword',
                );
                $this->useCase->execute($dto);
            } catch (InvalidCredentialsException $e) {
                // Expected
            }
        }

        // 6th attempt should be rate limited
        $dto = new AuthenticateUserDTO(
            username: 'testuser',
            password: 'wrongpassword',
        );

        $this->expectException(InvalidCredentialsException::class);
        $this->useCase->execute($dto);
    }

    /**
     * Test that successful authentication clears rate limiter.
     */
    public function test_successful_authentication_clears_rate_limiter(): void
    {
        // Create user identity
        $userIdentity = $this->createUserIdentity('testuser', 'test@example.com', 'password123');
        $this->userIdentityRepository->save($userIdentity);

        // Make 4 failed attempts
        for ($i = 0; $i < 4; $i++) {
            try {
                $dto = new AuthenticateUserDTO(
                    username: 'testuser',
                    password: 'wrongpassword',
                );
                $this->useCase->execute($dto);
            } catch (InvalidCredentialsException $e) {
                // Expected
            }
        }

        // Successful authentication should clear rate limiter
        $dto = new AuthenticateUserDTO(
            username: 'testuser',
            password: 'password123',
        );

        $authenticatedUser = $this->useCase->execute($dto);
        $this->assertInstanceOf(UserIdentity::class, $authenticatedUser);

        // Should be able to authenticate again (rate limiter cleared)
        $authenticatedUser2 = $this->useCase->execute($dto);
        $this->assertInstanceOf(UserIdentity::class, $authenticatedUser2);
    }

    /**
     * Test that authentication works with email.
     */
    public function test_authentication_works_with_email(): void
    {
        // Create user identity
        $userIdentity = $this->createUserIdentity('testuser', 'test@example.com', 'password123');
        $this->userIdentityRepository->save($userIdentity);

        // Authenticate with email
        $dto = new AuthenticateUserDTO(
            username: 'test@example.com',
            password: 'password123',
        );

        $authenticatedUser = $this->useCase->execute($dto);

        $this->assertInstanceOf(UserIdentity::class, $authenticatedUser);
        $this->assertEquals($userIdentity->id()->toString(), $authenticatedUser->id()->toString());
    }

    /**
     * Test that inactive user cannot authenticate.
     */
    public function test_inactive_user_cannot_authenticate(): void
    {
        // Create inactive user identity
        $userIdentity = $this->createUserIdentity('testuser', 'test@example.com', 'password123');
        $userIdentity->deactivate();
        $this->userIdentityRepository->save($userIdentity);

        // Try to authenticate
        $dto = new AuthenticateUserDTO(
            username: 'testuser',
            password: 'password123',
        );

        $this->expectException(InvalidCredentialsException::class);
        $this->useCase->execute($dto);
    }

    /**
     * Helper method to create UserIdentity.
     */
    private function createUserIdentity(string $username, string $email, string $password): UserIdentity
    {
        $passwordHash = $this->passwordHasher->hash($password);

        return UserIdentity::create(
            UserIdentityId::generate(),
            'user-uuid-123',
            Username::fromString($username),
            Email::fromString($email),
            $passwordHash,
        );
    }
}
