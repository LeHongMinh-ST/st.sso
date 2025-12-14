<?php

declare(strict_types=1);

namespace App\Livewire\Faculty;

use App\Models\Faculty;
use App\OrganizationalStructure\Application\DTOs\CreateFacultyDTO;
use App\OrganizationalStructure\Application\UseCases\CreateFacultyUseCase;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use RuntimeException;
use Throwable;

/**
 * Livewire component for creating a new faculty.
 * Refactored to use DDD Use Cases.
 */
class Create extends Component
{
    #[Validate(as: 'tên khoa')]
    public $name;

    #[Validate(as: 'mô tả')]
    public $description;

    private bool $isLoading = false;

    public function render()
    {
        return view('livewire.faculty.create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable|max:255',
        ];
    }

    public function submit()
    {
        if ($this->isLoading) {
            return;
        }

        if (!auth()->user()->can('create', Faculty::class)) {
            $this->dispatch('alert', type: 'error', message: 'Bạn không có quyền tạo khoa!');
            return;
        }

        try {
            $this->isLoading = true;
            $this->validate();

            // Create faculty using Use Case
            $createDTO = CreateFacultyDTO::fromArray([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            $faculty = $this->getCreateFacultyUseCase()->execute($createDTO);

            // Get integer ID for redirect (temporary until routes use UUID)
            $facultyModel = Faculty::where('uuid', $faculty->id()->toString())->first();
            $facultyId = $facultyModel?->id ?? $this->getFacultyIntegerId($faculty->id()->toString());

            session()->flash('success', 'Tạo mới thành công!');
            return redirect()->route('faculty.show', $facultyId);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Tạo mới thất bại: ' . $th->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Get CreateFacultyUseCase instance.
     * Livewire components cannot use constructor injection, so we use app() helper.
     *
     * @return CreateFacultyUseCase
     */
    private function getCreateFacultyUseCase(): CreateFacultyUseCase
    {
        return app(CreateFacultyUseCase::class);
    }

    /**
     * Get faculty integer ID from UUID.
     * Temporary helper until routes use UUID.
     *
     * @param string $facultyUuid Faculty UUID
     * @return int Faculty integer ID
     */
    private function getFacultyIntegerId(string $facultyUuid): int
    {
        $faculty = Faculty::where('uuid', $facultyUuid)->first();
        if (null !== $faculty) {
            return $faculty->id;
        }

        // Fallback: try to find by deterministic UUID
        $faculties = Faculty::all();
        foreach ($faculties as $f) {
            $generatedUuid = $this->generateDeterministicUuid('faculties', $f->id);
            if ($generatedUuid === $facultyUuid) {
                return $f->id;
            }
        }

        throw new RuntimeException("Faculty with UUID {$facultyUuid} not found");
    }

    /**
     * Generate deterministic UUID from integer ID.
     * Temporary helper until UUID migration is complete.
     *
     * @param string $table Table name
     * @param int $integerId Integer ID
     * @return string UUID string
     */
    private function generateDeterministicUuid(string $table, int $integerId): string
    {
        $namespace = \Ramsey\Uuid\Uuid::fromString('6ba7b810-9dad-11d1-80b4-00c04fd430c8');
        $name = "{$table}:{$integerId}";

        return \Ramsey\Uuid\Uuid::uuid5($namespace, $name)->toString();
    }
}
