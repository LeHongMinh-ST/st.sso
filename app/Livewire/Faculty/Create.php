<?php

declare(strict_types=1);

namespace App\Livewire\Faculty;

use App\Models\Faculty;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

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

            $faculty = Faculty::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            session()->flash('success', 'Tạo mới thành công!');
            return redirect()->route('faculty.show', $faculty->id);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Tạo mới thất bại!');
        } finally {
            $this->isLoading = false;
        }
    }
}
