<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\OrganizationalStructure\Domain\Aggregates\User;
use App\OrganizationalStructure\Domain\Exceptions\UserNotFoundException;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\UserId;

/**
 * Use case for finding a user.
 */
final class FindUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Find user by ID.
     *
     * @param string $userId User ID (UUID)
     * @return User User aggregate
     * @throws UserNotFoundException
     */
    public function execute(string $userId): User
    {
        $user = $this->userRepository->findById(UserId::fromString($userId));

        if (null === $user) {
            throw UserNotFoundException::withId($userId);
        }

        return $user;
    }
}
