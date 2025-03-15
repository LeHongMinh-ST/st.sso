<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            if (!Schema::hasColumn('oauth_clients', 'allowed_roles')) {
                $table->string('allowed_roles')->nullable()->default(json_encode(['super_admin']));
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            if (Schema::hasColumn('oauth_clients', 'allowed_roles')) {
                $table->dropColumn('allowed_roles');
            }
        });
    }
};
