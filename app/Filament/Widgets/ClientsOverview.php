<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\Status;
use App\Models\Client;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Schema;

class ClientsOverview extends Widget
{
    protected static string $view = 'filament.widgets.clients-overview';

    protected int | string | array $columnSpan = 'full';

    public function getClients()
    {
        $query = Client::query()
            ->where('is_show_dashboard', true);

        // Nếu cột status tồn tại, thêm điều kiện lọc theo status
        if (Schema::hasColumn('oauth_clients', 'status')) {
            $query->where(function ($q): void {
                $q->where('status', Status::Active)
                    ->orWhereNull('status');
            });
        }

        return $query->get();
    }
}
