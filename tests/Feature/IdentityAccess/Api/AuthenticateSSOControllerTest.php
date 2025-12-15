<?php

declare(strict_types=1);

namespace Tests\Feature\IdentityAccess\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Feature tests for SSO Authentication API endpoints.
 * Tests token issuance, validation, and revocation.
 */
final class AuthenticateSSOControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear rate limiter before each test
        RateLimiter::clear('issue_token:127.0.0.1');
        RateLimiter::clear('validate_token:127.0.0.1');
        RateLimiter::clear('revoke_token:127.0.0.1');
    }

    /**
     * Test that issueToken requires all required fields.
     */
    public function test_issue_token_requires_all_fields(): void
    {
        $response = $this->postJson('/api/oauth/token', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['grant_type', 'code', 'client_id', 'client_secret', 'redirect_uri']);
    }

    /**
     * Test that validateToken requires token.
     */
    public function test_validate_token_requires_token(): void
    {
        $response = $this->getJson('/api/oauth/token/validate');

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'invalid_request',
            'error_description' => 'Token not provided',
        ]);
    }

    /**
     * Test that validateToken returns invalid_token for invalid token.
     */
    public function test_validate_token_returns_invalid_for_invalid_token(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/api/oauth/token/validate');

        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'invalid_token',
            'error_description' => 'Token is invalid or expired',
        ]);
    }

    /**
     * Test that revokeToken requires token.
     */
    public function test_revoke_token_requires_token(): void
    {
        $response = $this->postJson('/api/oauth/token/revoke', []);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'invalid_request',
            'error_description' => 'Token not provided',
        ]);
    }

    /**
     * Test that revokeToken returns success even for invalid token (security).
     */
    public function test_revoke_token_returns_success_for_invalid_token(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->postJson('/api/oauth/token/revoke');

        // Should return success to prevent token enumeration
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Token revoked successfully',
        ]);
    }

    /**
     * Test rate limiting for issueToken.
     */
    public function test_issue_token_rate_limiting(): void
    {
        // Make 5 requests
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/oauth/token', [
                'grant_type' => 'authorization_code',
                'code' => 'test-code',
                'client_id' => 'test-client',
                'client_secret' => 'test-secret',
                'redirect_uri' => 'https://example.com/callback',
            ]);
        }

        // 6th request should be rate limited
        $response = $this->postJson('/api/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => 'test-code',
            'client_id' => 'test-client',
            'client_secret' => 'test-secret',
            'redirect_uri' => 'https://example.com/callback',
        ]);

        $response->assertStatus(429);
    }

    /**
     * Test rate limiting for validateToken.
     */
    public function test_validate_token_rate_limiting(): void
    {
        // Make 10 requests
        for ($i = 0; $i < 10; $i++) {
            $this->withHeader('Authorization', 'Bearer test-token')
                ->getJson('/api/oauth/token/validate');
        }

        // 11th request should be rate limited
        $response = $this->withHeader('Authorization', 'Bearer test-token')
            ->getJson('/api/oauth/token/validate');

        $response->assertStatus(429);
    }
}
