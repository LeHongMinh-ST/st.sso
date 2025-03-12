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
            if (!Schema::hasColumn('oauth_clients', 'description')) {
                $table->text('description')->nullable()->after('name');
            }

            if (!Schema::hasColumn('oauth_clients', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('thumbnail');
        });
    }
};
