<?php

declare(strict_types=1);

namespace App\Livewire\Client;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\ClientRepository;
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
    public string $description;

    public string $id;

    public string $secret;

    private bool $isLoading = false;

    public function rules(): array
    {
        return [
            'name' => 'required',
            'redirect' => 'required',
        ];
    }

    public function render()
    {
        return view('livewire.client.create');
    }

    public function submit(): void
    {
        if ($this->isLoading) {
            return;
        }
        try {
            $this->isLoading = true;
            $this->validate();

            $client = app(ClientRepository::class)->create(Auth::user()->id, $this->name, $this->redirect);
            $client->description = $this->description;
            $client->save();

            $this->id = $client->id;
            $this->secret = $client->secret;

            $this->openSuccessModal();
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('error', ['message' => 'Tạo ứng dụng thất bại']);
        } finally {
            $this->isLoading = false;
        }
    }

    public function openSuccessModal(): void
    {
        $this->dispatch('open-success-modal');
    }
}
