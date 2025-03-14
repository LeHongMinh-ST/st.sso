<?php

declare(strict_types=1);

namespace App\Livewire\Client;

use App\Helpers\Constants;
use App\Models\Client;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function render()
    {
        $clients = Client::query()
            ->search($this->search)
            ->paginate(Constants::PER_PAGE_ADMIN);

        return view('livewire.client.index', [
            'clients' => $clients
        ]);
    }

    public function toggleShowDashboard($id)
    {
        $client = Client::find($id);
        if (!$client) {
            $this->dispatch('error', ['message' => 'Có lỗi xảy ra vui lòng thử lại sau']);
            return;
        }
        $client->is_show_dashboard = !$client->is_show_dashboard;
        $client->save();
        
        // Clear all user dashboard caches
        $users = User::all();
        foreach ($users as $user) {
            cache()->forget('dashboard.clients.' . $user->id);
        }
    }
}
