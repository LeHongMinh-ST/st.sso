<?php

declare(strict_types=1);

namespace App\Livewire\Faculty;

use App\Helpers\Constants;
use App\Models\Faculty;
use Livewire\WithPagination;

use Livewire\Component;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function render()
    {
        $faculties = Faculty::query()
            ->search($this->search)
            ->paginate(Constants::PER_PAGE);

        return view('livewire.faculty.index', [
            'faculties' => $faculties,
        ]);
    }
}
