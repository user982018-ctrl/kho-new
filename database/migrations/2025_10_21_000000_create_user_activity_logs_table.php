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
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('ID người dùng');
            $table->string('user_name')->nullable()->comment('Tên người dùng');
            $table->string('action')->comment('Hành động (create, update, delete, view, login, logout)');
            $table->string('module')->comment('Module (orders, products, sale_care, users, etc.)');
            $table->string('record_id')->nullable()->comment('ID bản ghi bị tác động');
            $table->text('description')->nullable()->comment('Mô tả chi tiết');
            $table->string('ip_address', 45)->nullable()->comment('Địa chỉ IP');
            $table->string('user_agent')->nullable()->comment('User Agent');
            $table->text('url')->nullable()->comment('URL được truy cập');
            $table->string('method', 10)->nullable()->comment('HTTP Method (GET, POST, PUT, DELETE)');
            $table->json('old_values')->nullable()->comment('Giá trị cũ (trước khi thay đổi)');
            $table->json('new_values')->nullable()->comment('Giá trị mới (sau khi thay đổi)');
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('action');
            $table->index('module');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
    }
};

