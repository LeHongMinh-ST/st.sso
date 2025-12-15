<?php

declare(strict_types=1);

namespace App\Livewire\Client;

use App\Enums\Role;
use App\IdentityAccess\Application\UseCases\RegisterClientUseCase;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class Create extends Component
{
    #[Validate(as: 'tên client')]
    public string $name;

    #[Validate(as: 'redirect url')]
    public string $redirect;

    #[Validate(as: 'mô tả')]
    public string $description = '';

    public array $allowed_roles = [Role::SuperAdmin->value];

    private bool $isLoading = false;

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'redirect' => 'required|max:255|url',
        ];
    }

    public function render()
    {
        return view('livewire.client.create');
    }

    public function submit()
    {
        if ($this->isLoading) {
            return;
        }

        if (!auth()->user()->can('create', Client::class)) {
            $this->dispatch('alert', type: 'error', message: 'Bạn không có quyền tạo ứng dụng!');
            return;
        }

        try {
            $this->isLoading = true;
            $this->validate();

            // Use RegisterClientUseCase
            $registerClientUseCase = $this->getRegisterClientUseCase();
            $dto = new \App\IdentityAccess\Application\DTOs\RegisterClientDTO(
                name: $this->name,
                redirectUri: $this->redirect,
                description: $this->description,
                allowedRoles: $this->allowed_roles,
                grantTypes: ['authorization_code', 'refresh_token'],
                scopes: [],
            );

            $client = $registerClientUseCase->execute($dto);

            session()->flash('success', 'Tạo ứng dụng thành công!');

            // Get integer ID for redirect (Laravel Passport uses string IDs)
            return redirect()->route('client.show', $client->id()->toString());
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Tạo mới thất bại!');
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Get RegisterClientUseCase instance.
     * Uses app() helper as per Livewire convention.
     *
     * @return RegisterClientUseCase
     */
    private function getRegisterClientUseCase(): RegisterClientUseCase
    {
        return app(RegisterClientUseCase::class);
    }
}
