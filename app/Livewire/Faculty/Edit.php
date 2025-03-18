<?php

namespace App\Livewire\Faculty;

use App\Enums\Status;
use Livewire\Component;
use App\Models\Faculty;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Throwable;

class Edit extends Component
{
    #[Validate(as: 'tên khoa')]
    public string $name;

    #[Validate(as: 'mô tả')]
    public string $description;

    public Status $status = Status::Active;

    private bool $isLoading = false;

    public Faculty $faculty;

    public function render()
    {
        return view('livewire.faculty.edit');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable|max:255',
        ];
    }

    public function mount(Faculty $faculty)
    {
        $this->faculty = $faculty;
        $this->name = $faculty->name;
        $this->description = $faculty->description;
        $this->status = $faculty->status;
    }

    public function submit()
    {
        if ($this->isLoading) {
            return;
        }
        try {
            $this->isLoading = true;
            $this->validate();

            $this->faculty->update([
                'name' => $this->name,
                'description' => $this->description,
                'status' => $this->status->value
            ]);

            session()->flash('success', 'Cập nhật thành công!');
            return redirect()->route('faculty.show', $this->faculty->id);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Cập nhật thất bại!');
        } finally {
            $this->isLoading = false;
        }
    }

    public function toggleStatus()
    {
        $this->status = $this->status == Status::Active
            ? Status::Inactive
            : Status::Active;
    }
}
