<?php

declare(strict_types=1);

namespace Tests\Feature\IdentityAccess;

use App\IdentityAccess\Application\DTOs\CreateRoleDTO;
use App\IdentityAccess\Application\UseCases\CreateRoleUseCase;
use App\IdentityAccess\Domain\Aggregates\Role;
use App\IdentityAccess\Domain\Repositories\RoleRepositoryInterface;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for CreateRoleUseCase.
 * Tests role creation flow and domain events.
 */
final class CreateRoleUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private CreateRoleUseCase $useCase;
    private RoleRepositoryInterface $roleRepository;
    private OutboxEventRepositoryInterface $outboxEventRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleRepository = $this->app->make(RoleRepositoryInterface::class);
        $this->outboxEventRepository = $this->app->make(OutboxEventRepositoryInterface::class);

        $this->useCase = new CreateRoleUseCase(
            $this->roleRepository,
            $this->outboxEventRepository,
        );
    }

    /**
     * Test that role is created successfully.
     */
    public function test_role_is_created_successfully(): void
    {
        $dto = new CreateRoleDTO(
            name: 'admin',
            displayName: 'Administrator',
            description: 'System administrator role',
        );

        $role = $this->useCase->execute($dto);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('admin', $role->name());
        $this->assertEquals('Administrator', $role->displayName());
        $this->assertEquals('System administrator role', $role->description());
    }

    /**
     * Test that role is saved to repository.
     */
    public function test_role_is_saved_to_repository(): void
    {
        $dto = new CreateRoleDTO(
            name: 'admin',
            displayName: 'Administrator',
            description: 'System administrator role',
        );

        $role = $this->useCase->execute($dto);

        // Verify role can be retrieved
        $savedRole = $this->roleRepository->findById($role->id());

        $this->assertInstanceOf(Role::class, $savedRole);
        $this->assertEquals($role->id()->toString(), $savedRole->id()->toString());
        $this->assertEquals('admin', $savedRole->name());
    }

    /**
     * Test that role creation records domain event.
     */
    public function test_role_creation_records_domain_event(): void
    {
        $dto = new CreateRoleDTO(
            name: 'admin',
            displayName: 'Administrator',
            description: 'System administrator role',
        );

        $role = $this->useCase->execute($dto);

        // Check domain events
        $events = $role->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(\App\IdentityAccess\Domain\Events\RoleWasCreated::class, $events[0]);
    }

    /**
     * Test that role name is normalized.
     */
    public function test_role_name_is_normalized(): void
    {
        $dto = new CreateRoleDTO(
            name: '  ADMIN  ',
            displayName: 'Administrator',
            description: null,
        );

        $role = $this->useCase->execute($dto);

        $this->assertEquals('ADMIN', $role->name()); // Trimmed but not lowercased (as per Role aggregate)
    }

    /**
     * Test that role can be created without description.
     */
    public function test_role_can_be_created_without_description(): void
    {
        $dto = new CreateRoleDTO(
            name: 'user',
            displayName: 'User',
            description: null,
        );

        $role = $this->useCase->execute($dto);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertNull($role->description());
    }
}
