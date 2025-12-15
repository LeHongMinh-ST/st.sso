<?php

declare(strict_types=1);

namespace Tests\Feature\IdentityAccess\Http\Controllers;

use App\IdentityAccess\Domain\Aggregates\UserIdentity;
use App\IdentityAccess\Domain\Services\PasswordHasherInterface;
use App\IdentityAccess\Domain\ValueObjects\UserIdentityId;
use App\IdentityAccess\Domain\ValueObjects\Username;
use App\OrganizationalStructure\Infrastructure\Eloquent\User as EloquentUser;
use App\SharedKernel\Domain\ValueObjects\Email;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Feature tests for AuthenticateController.
 * Tests authentication flow, rate limiting, and security measures.
 */
final class AuthenticateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear rate limiter before each test
        RateLimiter::clear('authenticate:testuser');
        RateLimiter::clear('authenticate:test@example.com');
    }

    /**
     * Test that show login form returns view.
     */
    public function test_show_login_form_returns_view(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('pages.auth.login');
    }

    /**
     * Test that show login form redirects if authenticated.
     */
    public function test_show_login_form_redirects_if_authenticated(): void
    {
        $user = EloquentUser::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test that login succeeds with valid credentials.
     */
    public function test_login_succeeds_with_valid_credentials(): void
    {
        // Create user and user identity
        $user = $this->createUserWithIdentity('testuser', 'test@example.com', 'password123');

        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test that login fails with invalid password.
     */
    public function test_login_fails_with_invalid_password(): void
    {
        $this->createUserWithIdentity('testuser', 'test@example.com', 'password123');

        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertRedirect();
        $response->assertSessionHasErrors('message');
    }

    /**
     * Test that login fails with non-existent user.
     */
    public function test_login_fails_with_non_existent_user(): void
    {
        $response = $this->post('/login', [
            'username' => 'nonexistent',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect();
        $response->assertSessionHasErrors('message');
    }

    /**
     * Test that login works with email.
     */
    public function test_login_works_with_email(): void
    {
        $user = $this->createUserWithIdentity('testuser', 'test@example.com', 'password123');

        $response = $this->post('/login', [
            'username' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test that logout logs out user.
     */
    public function test_logout_logs_out_user(): void
    {
        $user = EloquentUser::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that rate limiting prevents brute force attacks.
     */
    public function test_rate_limiting_prevents_brute_force_attacks(): void
    {
        $this->createUserWithIdentity('testuser', 'test@example.com', 'password123');

        // Make 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'username' => 'testuser',
                'password' => 'wrongpassword',
            ]);
        }

        // 6th attempt should be rate limited
        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertRedirect();
        $response->assertSessionHasErrors('message');
    }

    /**
     * Test that remember me works.
     */
    public function test_remember_me_works(): void
    {
        $user = $this->createUserWithIdentity('testuser', 'test@example.com', 'password123');

        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'password123',
            'remember' => true,
        ]);

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Helper method to create user with user identity.
     */
    private function createUserWithIdentity(string $username, string $email, string $password): EloquentUser
    {
        $user = EloquentUser::factory()->create([
            'email' => $email,
        ]);

        // Create user identity
        $passwordHasher = app(PasswordHasherInterface::class);
        $passwordHash = $passwordHasher->hash($password);

        $userIdentity = UserIdentity::create(
            UserIdentityId::generate(),
            (string) $user->id, // organizationalStructureUserId
            Username::fromString($username),
            Email::fromString($email),
            $passwordHash,
        );

        // Save user identity
        $userIdentityRepository = app(\App\IdentityAccess\Domain\Repositories\UserIdentityRepositoryInterface::class);
        $userIdentityRepository->save($userIdentity);

        return $user;
    }
}
