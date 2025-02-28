<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('azure_ad_oid')->unique()->nullable()->after('email_verified_at');
            $table->text('azure_ad_access_token')->nullable()->after('azure_ad_oid');
            $table->text('azure_ad_refresh_token')->nullable()->after('azure_ad_access_token');
            $table->timestamp('azure_ad_expires_at')->nullable()->after('azure_ad_refresh_token');
            $table->string('provider', 50)->default('system')->after('azure_ad_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'azure_ad_oid',
                'azure_ad_access_token',
                'azure_ad_refresh_token',
                'azure_ad_expires_at',
                'provider'
            ]);
        });
    }
};