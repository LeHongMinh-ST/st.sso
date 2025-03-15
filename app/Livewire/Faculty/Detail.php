<?php

declare(strict_types=1);

namespace App\Livewire\Faculty;

use Livewire\Component;
use App\Models\Faculty;

class Detail extends Component
{
    public Faculty $faculty;

    public function render()
    {
        return view('livewire.faculty.detail');
    }

    public function mount($faculty)
    {
        $this->faculty = $faculty;
    }
    
}
