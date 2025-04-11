<?php

declare(strict_types=1);

namespace App\Livewire\Client;

use App\Enums\Role;
use App\Models\Client;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public Client $client;

    #[Validate(as: 'tên client')]
    public string $name;
    #[Validate(as: 'redirect url')]
    public string $redirect;
    #[Validate(as: 'mô tả')]
    public string $description;

    public array $allowed_roles = [Role::SuperAdmin->value];

    public function render()
    {
        return view('livewire.client.edit');
    }

    public function mount(Client $client): void
    {
        $this->client = $client;
        $this->name = $client->name ?? '';
        $this->redirect = $client->redirect ?? '';
        $this->description = $client->description ?? '';
        $this->allowed_roles = $client->allowed_roles ? $client->allowed_roles : [Role::SuperAdmin->value];
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'redirect' => 'required|max:255',
        ];
    }

    public function submit(): void
    {
        if (!auth()->user()->can('client.edit')) {
            $this->dispatch('alert', type: 'error', message: 'Bạn không có quyền chỉnh sửa ứng dụng!');
            return;
        }

        $this->validate();

        $this->client->update([
            'name' => $this->name,
            'redirect' => $this->redirect,
            'description' => $this->description,
            'allowed_roles' => $this->allowed_roles,
        ]);

        $this->dispatch('alert', type: 'success', message: 'Cập nhật thành công!');
    }
}
