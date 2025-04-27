<?php

declare(strict_types=1);

use App\Enums\Status;
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
            if (!Schema::hasColumn('oauth_clients', 'status')) {
                $table->string('status')->default(Status::Active->value)->after('revoked');
            }

            if (!Schema::hasColumn('oauth_clients', 'logo')) {
                $table->string('logo')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table): void {
            if (Schema::hasColumn('oauth_clients', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('oauth_clients', 'logo')) {
                $table->dropColumn('logo');
            }
        });
    }
};
