<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->unsignedInteger('low_stock_threshold')->default(10);
            $table->unsignedTinyInteger('is_active')->default(1);
            $table->timestamps();
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->index('name');
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
