<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // 1. Khóa ngoại liên kết tới bảng categories
            // constrained() tự hiểu là trỏ tới cột 'id' của bảng 'categories'
            // onDelete('cascade'): Xóa danh mục thì các sản phẩm thuộc danh mục đó tự xóa theo
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('cascade');

            $table->string('name');
            $table->string('slug')->unique(); 
            $table->string('sku')->unique();  // Mã quản lý kho (ví dụ: SP-001)
            $table->text('description')->nullable();

            // kiểu text thì lớn hơn string


            // 2. GIÁ TIỀN (Bản chất quan trọng)
            // Dùng unsignedBigInteger lưu số nguyên (VND). KHÔNG dùng float/double để tránh lỗi dấu chấm động.
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('sale_price')->nullable(); // Giá giảm (nếu có)

            $table->unsignedInteger('stock')->default(0); // Số lượng tồn kho

            // chèn từ khóa khác thì không nhận
            $table->enum('status', ['draft', 'published', 'out_of_stock'])->default('draft'); // Trạng thái

            $table->timestamps();

            // Đánh Index cho cặp (category_id, status) để khi truy vấn sản phẩm theo danh mục cực nhanh
            $table->index(['category_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
