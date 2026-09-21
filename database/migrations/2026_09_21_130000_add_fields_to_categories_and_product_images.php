<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->text('description')->nullable()->after('slug');
        });

        // Rename 'path' to 'image_url' in product_images for consistency
        Schema::table('product_images', function (Blueprint $table): void {
            $table->renameColumn('path', 'image_url');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn('description');
        });

        Schema::table('product_images', function (Blueprint $table): void {
            $table->renameColumn('image_url', 'path');
        });
    }
};
