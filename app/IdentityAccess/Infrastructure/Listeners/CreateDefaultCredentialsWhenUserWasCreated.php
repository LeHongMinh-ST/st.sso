<?php

declare(strict_types=1);

namespace App\IdentityAccess\Infrastructure\Listeners;

use App\IdentityAccess\Application\DTOs\CreateDefaultCredentialsDTO;
use App\IdentityAccess\Application\UseCases\CreateDefaultCredentialsUseCase;
use App\OrganizationalStructure\Domain\Events\UserWasCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Listener for UserWasCreated event from OrganizationalStructure context.
 * Creates default authentication credentials when a user is created.
 *
 * SECURITY: Creates UserIdentity with hashed password, never stores plain password.
 */
final class CreateDefaultCredentialsWhenUserWasCreated implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @param CreateDefaultCredentialsUseCase $createDefaultCredentialsUseCase
     */
    public function __construct(
        private readonly CreateDefaultCredentialsUseCase $createDefaultCredentialsUseCase,
    ) {
    }

    /**
     * Handle the event.
     *
     * @param UserWasCreated $event UserWasCreated event
     * @return void
     */
    public function handle(UserWasCreated $event): void
    {
        try {
            $dto = new CreateDefaultCredentialsDTO(
                userId: $event->userId,
                email: $event->email,
                defaultPassword: config('auth.default_password', 'password'), // Configurable default password
            );

            $this->createDefaultCredentialsUseCase->execute($dto);
        } catch (Throwable $e) {
            Log::error('Failed to create default credentials for user', [
                'user_id' => $event->userId,
                'email' => $event->email,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger queue retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param UserWasCreated $event UserWasCreated event
     * @param Throwable $exception Exception that caused the failure
     * @return void
     */
    public function failed(UserWasCreated $event, Throwable $exception): void
    {
        Log::error('Failed to create default credentials after retries', [
            'user_id' => $event->userId,
            'email' => $event->email,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Optionally, send notification to admin or create a failed job record
    }
}
