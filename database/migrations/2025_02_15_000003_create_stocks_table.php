<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('reserved_quantity')->default(0);
            $table->timestamps();

            $table->unique(['warehouse_id', 'inventory_item_id']);
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->index('quantity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
