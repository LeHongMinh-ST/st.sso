<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Application\UseCases;

use App\OrganizationalStructure\Application\DTOs\CreateUserDTO;
use App\OrganizationalStructure\Application\DTOs\ImportUserRowDTO;
use App\OrganizationalStructure\Application\DTOs\UpdateUserProfileDTO;
use App\OrganizationalStructure\Domain\Aggregates\User;
use App\OrganizationalStructure\Domain\Repositories\UserRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use App\OrganizationalStructure\Domain\ValueObjects\UserCode;
use App\OrganizationalStructure\Domain\ValueObjects\UserName;
use App\SharedKernel\Domain\ValueObjects\Email;
use App\SharedKernel\Domain\ValueObjects\Uuid;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Use case for importing users from Excel file.
 * Handles bulk import of users with progress tracking.
 */
final class ImportUsersFromExcelUseCase
{
    /**
     * @param UserRepositoryInterface $userRepository User repository
     * @param CreateUserUseCase $createUserUseCase Create user use case
     * @param UpdateUserProfileUseCase $updateUserProfileUseCase Update user profile use case
     * @param AssignUserToFacultyUseCase $assignUserToFacultyUseCase Assign user to faculty use case
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly CreateUserUseCase $createUserUseCase,
        private readonly UpdateUserProfileUseCase $updateUserProfileUseCase,
        private readonly AssignUserToFacultyUseCase $assignUserToFacultyUseCase,
    ) {
    }

    /**
     * Import users from Excel collection.
     *
     * @param Collection $rows Excel rows collection
     * @param string $facultyId Faculty ID (UUID)
     * @return array{imported: int, errors: int, errors_detail: array<int, array{row: array, error: string}>}
     */
    public function execute(Collection $rows, string $facultyId): array
    {
        $imported = 0;
        $errors = 0;
        $errorsDetail = [];

        // Pre-fetch existing users by email and code for optimization
        $emails = $rows->pluck('email')->filter()->unique()->toArray();
        $codes = $rows->pluck('ma_sinh_vien')->filter()->unique()->toArray();

        $existingUsersByEmail = [];
        $existingUsersByCode = [];

        if (!empty($emails)) {
            foreach ($emails as $email) {
                try {
                    $user = $this->userRepository->findByEmail(Email::fromString($email));
                    if (null !== $user) {
                        $existingUsersByEmail[$email] = $user;
                    }
                } catch (Exception $e) {
                    // Skip invalid emails
                }
            }
        }

        if (!empty($codes)) {
            foreach ($codes as $code) {
                try {
                    $user = $this->userRepository->findByUserCode(UserCode::fromString($code));
                    if (null !== $user) {
                        $existingUsersByCode[$code] = $user;
                    }
                } catch (Exception $e) {
                    // Skip invalid codes
                }
            }
        }

        // Process each row
        foreach ($rows as $index => $row) {
            try {
                $rowDTO = ImportUserRowDTO::fromArray($row->toArray());

                // Validate required fields
                if (empty($rowDTO->firstName) || empty($rowDTO->lastName) || empty($rowDTO->email) || empty($rowDTO->userCode)) {
                    throw new InvalidArgumentException('Missing required fields: first_name, last_name, email, or user_code');
                }

                // Check if user exists by code (preferred) or email
                $existingUser = $existingUsersByCode[$rowDTO->userCode] ?? $existingUsersByEmail[$rowDTO->email] ?? null;

                if (null !== $existingUser) {
                    // Update existing user
                    $updateDTO = UpdateUserProfileDTO::fromArray([
                        'first_name' => $rowDTO->firstName,
                        'last_name' => $rowDTO->lastName,
                        'email' => $rowDTO->email,
                        'phone' => $rowDTO->phone,
                    ]);

                    $this->updateUserProfileUseCase->execute($existingUser->id()->toString(), $updateDTO);

                    // Assign to faculty if not already assigned
                    $facultyIdVO = FacultyId::fromString($facultyId);
                    if (null === $existingUser->facultyId() || !$existingUser->facultyId()->equals($facultyIdVO)) {
                        $this->assignUserToFacultyUseCase->execute($existingUser->id()->toString(), $facultyId);
                    }

                    $imported++;
                } else {
                    // Create new user
                    // Generate username from email
                    $userName = UserName::fromString($rowDTO->email);

                    $createDTO = CreateUserDTO::fromArray([
                        'user_name' => $rowDTO->email,
                        'first_name' => $rowDTO->firstName,
                        'last_name' => $rowDTO->lastName,
                        'email' => $rowDTO->email,
                        'user_code' => $rowDTO->userCode,
                        'phone' => $rowDTO->phone,
                        'faculty_id' => $facultyId,
                    ]);

                    $this->createUserUseCase->execute($createDTO);
                    $imported++;
                }
            } catch (Exception $e) {
                $errors++;
                $errorsDetail[] = [
                    'row' => $row->toArray(),
                    'error' => $e->getMessage(),
                ];
                Log::error('Import user error', [
                    'row' => $row->toArray(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        return [
            'imported' => $imported,
            'errors' => $errors,
            'errors_detail' => $errorsDetail,
        ];
    }
}
