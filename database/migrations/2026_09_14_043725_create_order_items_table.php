<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            // Khóa ngoại trỏ về bảng orders, xóa đơn -> xóa toàn bộ items liên quan
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            // Khóa ngoại trỏ về bảng products
            $table->foreignId('product_id')->constrained();

            $table->integer('quantity');
            $table->decimal('price', 12, 2); // Giá sản phẩm TẠI THỜI ĐIỂM MUA để nay mai mà update giá mới đỡ bị dính dáng
            $table->decimal('subtotal', 12, 2); // total = price * quantity
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};