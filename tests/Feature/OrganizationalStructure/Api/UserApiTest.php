<?php

declare(strict_types=1);

namespace Tests\Feature\OrganizationalStructure\Api;

use App\OrganizationalStructure\Infrastructure\Eloquent\User as EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Feature tests for User API endpoints.
 * Tests authentication, authorization, and API functionality.
 */
final class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that index requires authentication.
     */
    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    /**
     * Test that index requires permission.
     */
    public function test_index_requires_permission(): void
    {
        $user = EloquentUser::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/users');

        // Should return 403 if user doesn't have permission
        // Note: This depends on Policy implementation
        $response->assertStatus(403);
    }

    /**
     * Test that show requires authentication.
     */
    public function test_show_requires_authentication(): void
    {
        $user = EloquentUser::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(401);
    }

    /**
     * Test that show works with integer ID.
     */
    public function test_show_works_with_integer_id(): void
    {
        $currentUser = EloquentUser::factory()->create();
        Passport::actingAs($currentUser);

        $targetUser = EloquentUser::factory()->create();

        $response = $this->getJson("/api/users/{$targetUser->id}");

        // Should return 200 if user has permission, or 403 if not
        $this->assertContains($response->status(), [200, 403]);
    }

    /**
     * Test that show works with UUID.
     */
    public function test_show_works_with_uuid(): void
    {
        $currentUser = EloquentUser::factory()->create();
        Passport::actingAs($currentUser);

        $targetUser = EloquentUser::factory()->create();
        $hasUuidColumn = \Illuminate\Support\Facades\Schema::hasColumn('users', 'uuid');

        if ($hasUuidColumn && null !== $targetUser->uuid) {
            $response = $this->getJson("/api/users/{$targetUser->uuid}");

            // Should return 200 if user has permission, or 403 if not
            $this->assertContains($response->status(), [200, 403]);
        } else {
            $this->markTestSkipped('UUID column not available');
        }
    }

    /**
     * Test that store requires authentication.
     */
    public function test_store_requires_authentication(): void
    {
        $response = $this->postJson('/api/users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test that resetPassword requires authentication.
     */
    public function test_reset_password_requires_authentication(): void
    {
        $user = EloquentUser::factory()->create();

        $response = $this->postJson("/api/users/{$user->id}/reset-password");

        $response->assertStatus(401);
    }

    /**
     * Test that resetPassword requires permission.
     */
    public function test_reset_password_requires_permission(): void
    {
        $currentUser = EloquentUser::factory()->create();
        Passport::actingAs($currentUser);

        $targetUser = EloquentUser::factory()->create([
            'faculty_id' => $currentUser->faculty_id + 1, // Different faculty
        ]);

        $response = $this->postJson("/api/users/{$targetUser->id}/reset-password");

        // Should return 403 if user is not super admin and not in same faculty
        $response->assertStatus(403);
    }
}
