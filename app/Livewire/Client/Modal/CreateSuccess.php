<?php

declare(strict_types=1);

namespace App\Livewire\Client\Modal;

use Livewire\Component;

class CreateSuccess extends Component
{
    public string|null $clientId;

    public string|null $clientSecret;

    public function render()
    {
        return view('livewire.client.modal.create-success');
    }

    protected $listeners = [
        'onOpenCreateSuccessModal' => 'handleOpenCreateSuccessModal',
    ];

    public function closeSuccessModal()
    {
        $this->dispatch('onCloseCreateSuccessModal');
        return redirect()->route('client.index');
    }

    public function handleOpenCreateSuccessModal($data)
    {
        $this->clientId = $data['id'];
        $this->clientSecret = $data['secret'];
    }
}
