<?php

namespace App\Console\Commands;

use App\Enums\Role;
use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordDefault extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-password-default';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::where('role', Role::Student)->update([
            'password' => Hash::make('password'),
        ]);
        $this->info('Updated ' . $users . ' users');
    }
}
