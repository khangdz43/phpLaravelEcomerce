<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Cột parent_id: Để làm danh mục cha - con (ví dụ: Áo -> Áo Nam)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->onDelete('cascade');

            $table->string('name'); // Tên danh mục (ví dụ: Áo Nam)
            $table->string('slug')->unique(); // Đường dẫn URL (ví dụ: ao-nam)
            $table->boolean('is_active')->default(true); // Trạng thái ẩn/hiện
            $table->timestamps(); // Tạo 2 cột created_at và updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
