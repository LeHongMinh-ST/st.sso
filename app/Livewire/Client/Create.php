<?php

declare(strict_types=1);

namespace App\Livewire\Client;

use App\Enums\Role;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public string $description = '';

    public array $allowed_roles = [Role::SuperAdmin->value];

    private bool $isLoading = false;

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'redirect' => 'required|max:255',
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

        DB::beginTransaction();
        try {
            $this->isLoading = true;
            $this->validate();

            $client = app(ClientRepository::class)
                ->create(Auth::user()->id, $this->name, $this->redirect);

            Client::where('id', $client->id)->update([
                'description' => $this->description,
                'allowed_roles' => $this->allowed_roles,
            ]);

            DB::commit();
            session()->flash('success', 'Tạo ứng dụng thành công!');
            return redirect()->route('client.show', $client->id);
        } catch (Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Tạo mới thất bại!');
        } finally {
            $this->isLoading = false;
        }
    }
}
