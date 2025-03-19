<?php

declare(strict_types=1);

namespace App\Livewire\Faculty;

use App\Helpers\Constants;
use App\Models\Faculty;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
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
