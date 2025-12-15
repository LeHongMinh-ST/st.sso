<?php

declare(strict_types=1);

namespace Tests\Feature\IdentityAccess;

use App\IdentityAccess\Application\DTOs\RegisterClientDTO;
use App\IdentityAccess\Application\UseCases\RegisterClientUseCase;
use App\IdentityAccess\Domain\Aggregates\Client;
use App\IdentityAccess\Domain\Repositories\ClientRepositoryInterface;
use App\SharedKernel\Domain\Repositories\OutboxEventRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for RegisterClientUseCase.
 * Tests OAuth2 client registration flow and security.
 */
final class RegisterClientUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private RegisterClientUseCase $useCase;
    private ClientRepositoryInterface $clientRepository;
    private OutboxEventRepositoryInterface $outboxEventRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientRepository = $this->app->make(ClientRepositoryInterface::class);
        $this->outboxEventRepository = $this->app->make(OutboxEventRepositoryInterface::class);

        $this->useCase = new RegisterClientUseCase(
            $this->clientRepository,
            $this->outboxEventRepository,
        );
    }

    /**
     * Test that client is registered successfully.
     */
    public function test_client_is_registered_successfully(): void
    {
        $dto = new RegisterClientDTO(
            name: 'Test Client',
            redirectUri: 'https://example.com/callback',
            isPersonalAccessClient: false,
            isPasswordClient: false,
            allowedRoles: [],
            grantTypes: ['authorization_code'],
            scopes: ['read', 'write'],
            description: 'Test client application',
            thumbnail: null,
            provider: null,
        );

        $client = $this->useCase->execute($dto);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('Test Client', $client->name());
        $this->assertEquals('https://example.com/callback', $client->redirectUri());
        $this->assertFalse($client->isRevoked());
    }

    /**
     * Test that client is saved to repository.
     */
    public function test_client_is_saved_to_repository(): void
    {
        $dto = new RegisterClientDTO(
            name: 'Test Client',
            redirectUri: 'https://example.com/callback',
            isPersonalAccessClient: false,
            isPasswordClient: false,
            allowedRoles: [],
            grantTypes: ['authorization_code'],
            scopes: ['read', 'write'],
            description: null,
            thumbnail: null,
            provider: null,
        );

        $client = $this->useCase->execute($dto);

        // Verify client can be retrieved
        $savedClient = $this->clientRepository->findById($client->id());

        $this->assertInstanceOf(Client::class, $savedClient);
        $this->assertEquals($client->id()->toString(), $savedClient->id()->toString());
        $this->assertEquals('Test Client', $savedClient->name());
    }

    /**
     * Test that client registration records domain event.
     */
    public function test_client_registration_records_domain_event(): void
    {
        $dto = new RegisterClientDTO(
            name: 'Test Client',
            redirectUri: 'https://example.com/callback',
            isPersonalAccessClient: false,
            isPasswordClient: false,
            allowedRoles: [],
            grantTypes: ['authorization_code'],
            scopes: ['read', 'write'],
            description: null,
            thumbnail: null,
            provider: null,
        );

        $client = $this->useCase->execute($dto);

        // Check domain events
        $events = $client->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(\App\IdentityAccess\Domain\Events\ClientWasRegistered::class, $events[0]);
    }

    /**
     * Test that invalid redirect URI throws exception.
     */
    public function test_invalid_redirect_uri_throws_exception(): void
    {
        $dto = new RegisterClientDTO(
            name: 'Test Client',
            redirectUri: 'not-a-valid-url',
            isPersonalAccessClient: false,
            isPasswordClient: false,
            allowedRoles: [],
            grantTypes: ['authorization_code'],
            scopes: ['read', 'write'],
            description: null,
            thumbnail: null,
            provider: null,
        );

        $this->expectException(\App\SharedKernel\Domain\Exceptions\InvalidArgumentException::class);
        $this->useCase->execute($dto);
    }

    /**
     * Test that non-http/https redirect URI throws exception.
     */
    public function test_non_http_redirect_uri_throws_exception(): void
    {
        $dto = new RegisterClientDTO(
            name: 'Test Client',
            redirectUri: 'javascript:alert("XSS")',
            isPersonalAccessClient: false,
            isPasswordClient: false,
            allowedRoles: [],
            grantTypes: ['authorization_code'],
            scopes: ['read', 'write'],
            description: null,
            thumbnail: null,
            provider: null,
        );

        $this->expectException(\App\SharedKernel\Domain\Exceptions\InvalidArgumentException::class);
        $this->useCase->execute($dto);
    }

    /**
     * Test that personal access client can be registered.
     */
    public function test_personal_access_client_can_be_registered(): void
    {
        $dto = new RegisterClientDTO(
            name: 'Personal Client',
            redirectUri: 'https://example.com/callback',
            isPersonalAccessClient: true,
            isPasswordClient: false,
            allowedRoles: [],
            grantTypes: ['personal_access'],
            scopes: ['*'],
            description: null,
            thumbnail: null,
            provider: null,
        );

        $client = $this->useCase->execute($dto);

        $this->assertTrue($client->isPersonalAccessClient());
    }

    /**
     * Test that password client can be registered.
     */
    public function test_password_client_can_be_registered(): void
    {
        $dto = new RegisterClientDTO(
            name: 'Password Client',
            redirectUri: 'https://example.com/callback',
            isPersonalAccessClient: false,
            isPasswordClient: true,
            allowedRoles: [],
            grantTypes: ['password'],
            scopes: ['read', 'write'],
            description: null,
            thumbnail: null,
            provider: null,
        );

        $client = $this->useCase->execute($dto);

        $this->assertTrue($client->isPasswordClient());
    }
}
