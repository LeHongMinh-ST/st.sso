<?php

declare(strict_types=1);

namespace App\Livewire\Client;

use App\Models\Client;
use App\OrganizationalStructure\Application\Services\PolicyAuthorizationServiceInterface;
use Livewire\Attributes\On;
use Livewire\Component;

class Detail extends Component
{
    public $client;

    public function render()
    {
        return view('livewire.client.detail');
    }

    public function mount(Client $client): void
    {
        $this->client = $client;
    }

    #[On('deleteClient')]
    public function delete()
    {
        $currentUser = auth()->user();
        if (null === $currentUser || !$this->getPolicyAuthorizationService()->canDelete($currentUser, $this->client)) {
            session()->flash('error', 'Bạn không có quyền xóa ứng dụng!');
            return;
        }

        $this->client->delete();
        session()->flash('success', 'Xoá thành công ứng dụng!');
        return redirect()->route('client.index');
    }

    public function openDeleteModal(): void
    {
        $currentUser = auth()->user();
        if (null === $currentUser || !$this->getPolicyAuthorizationService()->canDelete($currentUser, $this->client)) {
            return;
        }

        $this->dispatch('onOpenDeleteModal');
    }

    /**
     * Get PolicyAuthorizationService instance.
     * Livewire components cannot use constructor injection, so we use app() helper.
     *
     * @return PolicyAuthorizationServiceInterface
     */
    private function getPolicyAuthorizationService(): PolicyAuthorizationServiceInterface
    {
        return app(PolicyAuthorizationServiceInterface::class);
    }
}
