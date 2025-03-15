<?php

namespace App\Livewire\Faculty;

use Livewire\Component;
use App\Models\Faculty;

class Edit extends Component
{
    #[Validate(as: 'tên khoa')]
    public $name;

    #[Validate(as: 'mô tả')]
    public $description;

    private bool $isLoading = false;

    public Faculty $faculty;

    public function render()
    {
        return view('livewire.faculty.edit');
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'description' => 'nullable',
        ];
    }

    public function mount(Faculty $faculty)
    {
        $this->faculty = $faculty;
        $this->name = $faculty->name;
        $this->description = $faculty->description;
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
}
