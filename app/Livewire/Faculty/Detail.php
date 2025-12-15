<?php

declare(strict_types=1);

namespace App\Livewire\Faculty;

use App\OrganizationalStructure\Application\Services\PolicyAuthorizationServiceInterface;
use App\OrganizationalStructure\Infrastructure\Eloquent\Faculty;
use App\OrganizationalStructure\Infrastructure\Eloquent\User;
use App\SharedKernel\Helpers\Constants;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Detail extends Component
{
    use WithPagination;

    public Faculty $faculty;
    public string $search = '';
    public bool $showCreateUserForm = false;
    public bool $showImportStudentsForm = false;

    public function render()
    {
        $users = $this->faculty->users()
            ->search($this->search)
            ->orderBy('created_at', 'desc')
            ->paginate(Constants::PER_PAGE);

        return view('livewire.faculty.detail', [
            'users' => $users
        ]);
    }

    public function mount($faculty): void
    {
        $this->faculty = $faculty;
    }

    #[On('deleteFaculty')]
    public function delete()
    {
        $currentUser = auth()->user();
        if (null === $currentUser || !$this->getPolicyAuthorizationService()->canDelete($currentUser, $this->faculty)) {
            session()->flash('error', 'Bạn không có quyền xóa khoa!');
            return;
        }

        $this->faculty->delete();
        session()->flash('success', 'Xoá thành công!');
        return redirect()->route('faculty.index');
    }

    public function openDeleteModal(): void
    {
        $currentUser = auth()->user();
        if (null === $currentUser || !$this->getPolicyAuthorizationService()->canDelete($currentUser, $this->faculty)) {
            return;
        }
        $this->dispatch('onOpenDeleteModal');
    }

    public function toggleCreateUserForm(): void
    {
        $currentUser = auth()->user();
        if (null === $currentUser || !$this->getPolicyAuthorizationService()->canCreate($currentUser, User::class)) {
            return;
        }
        $this->showCreateUserForm = !$this->showCreateUserForm;
        $this->showImportStudentsForm = false;
    }

    public function toggleImportStudentsForm(): void
    {
        $currentUser = auth()->user();
        if (null === $currentUser || !$this->getPolicyAuthorizationService()->canCreate($currentUser, User::class)) {
            return;
        }
        $this->showImportStudentsForm = !$this->showImportStudentsForm;
        $this->showCreateUserForm = false;
    }

    #[On('closeImportForm')]
    public function closeImportForm(): void
    {
        $this->showImportStudentsForm = false;
    }

    #[On('userCreated')]
    #[On('studentsImported')]
    public function refreshUsers(): void
    {
        $this->resetPage();
    }

    /**
     * Get PolicyAuthorizationService instance.
     * Livewire components cannot use constructor injection, so we use app() helper.
     *
     * @return PolicyAuthorizationServiceInterface
     */
    private function getPolicyAuthorizationService(): PolicyAuthorizationServiceInterface
    {
        return app(PolicyAuthorizationServiceInterface::class);
    }
}
