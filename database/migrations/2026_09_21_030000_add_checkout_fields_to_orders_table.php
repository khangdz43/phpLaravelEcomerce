<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('coupon_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->unsignedBigInteger('subtotal_amount')->nullable()->after('shipping_address');
            $table->unsignedBigInteger('discount_amount')->default(0)->after('subtotal_amount');
            $table->string('payment_method')->default('cod')->after('status');
            $table->text('notes')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropColumn(['subtotal_amount', 'discount_amount', 'payment_method', 'notes']);
        });
    }
};
