<?php

declare(strict_types=1);

namespace App\Livewire\Client;

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

        DB::beginTransaction();
        try {
            $this->isLoading = true;
            $this->validate();

            $client = app(ClientRepository::class)
                ->create(Auth::user()->id, $this->name, $this->redirect);

            Client::where('id', $client->id)->update([
                'description' => $this->description
            ]);

            $this->dispatch('onOpenCreateSuccessModal', [
                'id' => $client->id,
                'secret' => $client->secret
            ]);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Tạo mới thất bại!');
        } finally {
            $this->isLoading = false;
        }
    }
}
