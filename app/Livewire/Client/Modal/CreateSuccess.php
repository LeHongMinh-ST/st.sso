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
        'open-success-modal' => 'handleOpenSuccess',
    ];

    public function closeSuccessModal()
    {
        $this->dispatch('close-success-modal');
        return redirect()->route('client.index');
    }

    public function handleOpenSuccess($data)
    {
        $this->clientId = $data['id'];
        $this->clientSecret = $data['secret'];
    }
}
