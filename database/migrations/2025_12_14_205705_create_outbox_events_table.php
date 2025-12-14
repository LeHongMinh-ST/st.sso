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
        Schema::create('outbox_events', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('aggregate_type', 100)->index();
            $table->uuid('aggregate_id')->index();
            $table->string('event_type', 255)->index();
            $table->json('payload');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('processed_at')->nullable()->index();
        });

        // Index for querying unprocessed events
        Schema::table('outbox_events', function (Blueprint $table): void {
            $table->index(['processed_at', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbox_events');
    }
};
