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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // create, update, delete, login, logout, etc.
            $table->string('model_type')->nullable(); // App\Models\User, App\Models\Client, etc.
            $table->string('model_id')->nullable(); // ID của model
            $table->longText('before')->nullable(); // Dữ liệu trước khi thay đổi
            $table->longText('after')->nullable(); // Dữ liệu sau khi thay đổi
            $table->string('ip_address')->nullable(); // Địa chỉ IP
            $table->text('user_agent')->nullable(); // Thông tin trình duyệt
            $table->text('description')->nullable(); // Mô tả hành động
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('action');
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
