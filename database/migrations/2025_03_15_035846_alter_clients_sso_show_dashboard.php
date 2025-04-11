<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table): void {
            if (!Schema::hasColumn('oauth_clients', 'is_show_dashboard')) {
                $table->tinyInteger('is_show_dashboard')->default(false);
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table): void {
            if (Schema::hasColumn('oauth_clients', 'is_show_dashboard')) {
                $table->dropColumn('is_show_dashboard');
            }
        });
    }
};
