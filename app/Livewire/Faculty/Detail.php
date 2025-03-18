<?php

declare(strict_types=1);

namespace App\Livewire\Faculty;

use Livewire\Component;
use App\Models\Faculty;

class Detail extends Component
{
    public Faculty $faculty;

    protected $listeners = [
        'deleteFaculty' => 'delete',
    ];

    public function render()
    {
        return view('livewire.faculty.detail');
    }

    public function mount($faculty)
    {
        $this->faculty = $faculty;
    }

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
