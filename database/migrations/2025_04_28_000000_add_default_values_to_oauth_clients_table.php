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
            // Add default values for required fields
            $table->boolean('personal_access_client')->default(false)->change();
            $table->boolean('password_client')->default(false)->change();
            $table->boolean('revoked')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table): void {
            // Remove default values
            $table->boolean('personal_access_client')->default(null)->change();
            $table->boolean('password_client')->default(null)->change();
            $table->boolean('revoked')->default(null)->change();
        });
    }
};
