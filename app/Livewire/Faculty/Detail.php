<?php

declare(strict_types=1);

namespace App\Livewire\Faculty;

use App\Models\Faculty;
use Livewire\Attributes\On;
use Livewire\Component;

class Detail extends Component
{
    public Faculty $faculty;

    public function render()
    {
        return view('livewire.faculty.detail');
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

}
