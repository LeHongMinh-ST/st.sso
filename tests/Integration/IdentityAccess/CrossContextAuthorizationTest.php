<?php

declare(strict_types=1);

namespace Tests\Integration\IdentityAccess;

use App\IdentityAccess\Application\Services\AuthorizationService;
use App\IdentityAccess\Domain\Aggregates\Permission;
use App\IdentityAccess\Domain\Aggregates\Role;
use App\IdentityAccess\Domain\Repositories\PermissionRepositoryInterface;
use App\IdentityAccess\Domain\Repositories\RoleRepositoryInterface;
use App\OrganizationalStructure\Infrastructure\Eloquent\User as EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integration tests for cross-context authorization.
 * Tests integration between OrganizationalStructure (User) and IdentityAccess (Permissions, Roles).
 */
final class CrossContextAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private AuthorizationService $authorizationService;
    private RoleRepositoryInterface $roleRepository;
    private PermissionRepositoryInterface $permissionRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizationService = app(AuthorizationService::class);
        $this->roleRepository = app(RoleRepositoryInterface::class);
        $this->permissionRepository = app(PermissionRepositoryInterface::class);
    }

    /**
     * Test that user with permission can access resource.
     */
    public function test_user_with_permission_can_access_resource(): void
    {
        // Create user
        $user = EloquentUser::factory()->create();

        // Create role and permission
        $role = $this->createRole('admin', 'Administrator');
        $permission = $this->createPermission('user.view', 'View Users');

        // Assign permission to role
        $role->assignPermission($permission->id()->toString());
        $this->roleRepository->save($role);

        // Assign role to user
        $this->assignRoleToUser($user, $role);

        // Check permission
        $canView = $this->authorizationService->can($user, 'user.view');

        $this->assertTrue($canView);
    }

    /**
     * Test that user without permission cannot access resource.
     */
    public function test_user_without_permission_cannot_access_resource(): void
    {
        // Create user
        $user = EloquentUser::factory()->create();

        // Create role without permission
        $role = $this->createRole('user', 'User');

        // Assign role to user
        $this->assignRoleToUser($user, $role);

        // Check permission
        $canView = $this->authorizationService->can($user, 'user.view');

        $this->assertFalse($canView);
    }

    /**
     * Test that user with multiple roles can access if any role has permission.
     */
    public function test_user_with_multiple_roles_can_access_if_any_role_has_permission(): void
    {
        // Create user
        $user = EloquentUser::factory()->create();

        // Create roles
        $role1 = $this->createRole('user', 'User');
        $role2 = $this->createRole('admin', 'Administrator');

        // Create permission
        $permission = $this->createPermission('user.view', 'View Users');

        // Assign permission to role2 only
        $role2->assignPermission($permission->id()->toString());
        $this->roleRepository->save($role2);

        // Assign both roles to user
        $this->assignRoleToUser($user, $role1);
        $this->assignRoleToUser($user, $role2);

        // Check permission
        $canView = $this->authorizationService->can($user, 'user.view');

        $this->assertTrue($canView);
    }

    /**
     * Test that canAny returns true if user has any permission.
     */
    public function test_can_any_returns_true_if_user_has_any_permission(): void
    {
        // Create user
        $user = EloquentUser::factory()->create();

        // Create role and permissions
        $role = $this->createRole('admin', 'Administrator');
        $permission1 = $this->createPermission('user.view', 'View Users');
        $permission2 = $this->createPermission('user.create', 'Create Users');

        // Assign only permission1 to role
        $role->assignPermission($permission1->id()->toString());
        $this->roleRepository->save($role);

        // Assign role to user
        $this->assignRoleToUser($user, $role);

        // Check canAny
        $canAny = $this->authorizationService->canAny($user, ['user.view', 'user.create']);

        $this->assertTrue($canAny);
    }

    /**
     * Test that canAll returns true only if user has all permissions.
     */
    public function test_can_all_returns_true_only_if_user_has_all_permissions(): void
    {
        // Create user
        $user = EloquentUser::factory()->create();

        // Create role and permissions
        $role = $this->createRole('admin', 'Administrator');
        $permission1 = $this->createPermission('user.view', 'View Users');
        $permission2 = $this->createPermission('user.create', 'Create Users');

        // Assign both permissions to role
        $role->assignPermission($permission1->id()->toString());
        $role->assignPermission($permission2->id()->toString());
        $this->roleRepository->save($role);

        // Assign role to user
        $this->assignRoleToUser($user, $role);

        // Check canAll
        $canAll = $this->authorizationService->canAll($user, ['user.view', 'user.create']);

        $this->assertTrue($canAll);
    }

    /**
     * Test that canAll returns false if user missing any permission.
     */
    public function test_can_all_returns_false_if_user_missing_any_permission(): void
    {
        // Create user
        $user = EloquentUser::factory()->create();

        // Create role and permissions
        $role = $this->createRole('admin', 'Administrator');
        $permission1 = $this->createPermission('user.view', 'View Users');
        $permission2 = $this->createPermission('user.create', 'Create Users');

        // Assign only permission1 to role
        $role->assignPermission($permission1->id()->toString());
        $this->roleRepository->save($role);

        // Assign role to user
        $this->assignRoleToUser($user, $role);

        // Check canAll
        $canAll = $this->authorizationService->canAll($user, ['user.view', 'user.create']);

        $this->assertFalse($canAll);
    }

    /**
     * Helper method to create role.
     */
    private function createRole(string $name, string $displayName): Role
    {
        $roleId = $this->roleRepository->nextIdentity();

        $role = Role::create(
            $roleId,
            $name,
            $displayName,
        );

        $this->roleRepository->save($role);

        return $role;
    }

    /**
     * Helper method to create permission.
     */
    private function createPermission(string $code, string $name): Permission
    {
        $permissionId = $this->permissionRepository->nextIdentity();

        $permission = Permission::create(
            $permissionId,
            $name, // name
            $code, // code
            $name, // displayName
        );

        $this->permissionRepository->save($permission);

        return $permission;
    }

    /**
     * Assign role to user.
     * Bridge method to assign DDD Role aggregate to Eloquent User model.
     */
    private function assignRoleToUser(EloquentUser $user, Role $role): void
    {
        // Find Eloquent Role model (App\IdentityAccess\Infrastructure\Eloquent\Role)
        $eloquentRole = \App\IdentityAccess\Infrastructure\Eloquent\Role::where('uuid', $role->id()->toString())->first();
        if (null === $eloquentRole) {
            // Fallback: try to find by name
            $eloquentRole = \App\IdentityAccess\Infrastructure\Eloquent\Role::where('name', $role->name())->first();
        }

        if (null !== $eloquentRole) {
            // User model uses App\Models\Role, so we need to find/create that model
            if (!$user->hasRole($eloquentRole->name)) {
                // Use App\Models\Role for user relationship
                $legacyRole = \App\Models\Role::find($eloquentRole->id);
                if (null === $legacyRole) {
                    // If App\Models\Role doesn't exist, create it
                    $legacyRole = \App\Models\Role::create([
                        'name' => $eloquentRole->name,
                        'display_name' => $eloquentRole->display_name,
                        'description' => $eloquentRole->description,
                    ]);
                }
                $user->roles()->attach($legacyRole->id);
            }
        }
    }
}
