<?php

declare(strict_types=1);

namespace App\Livewire\Faculty;

use App\Helpers\Constants;
use App\Models\Faculty;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Detail extends Component
{
    use WithPagination;

    public Faculty $faculty;
    public string $search = '';
    public bool $showCreateUserForm = false;

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
        $this->faculty->delete();
        session()->flash('success', 'Xoá thành công!');
        return redirect()->route('faculty.index');
    }

    public function openDeleteModal(): void
    {
        $this->dispatch('onOpenDeleteModal');
    }

    public function toggleCreateUserForm(): void
    {
        $this->showCreateUserForm = !$this->showCreateUserForm;
    }

    #[On('userCreated')]
    public function refreshUsers(): void
    {
        $this->resetPage();
    }
}
