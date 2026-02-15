<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('name');
            $table->string('location');
            $table->unsignedTinyInteger('is_active')->default(1);
            $table->timestamps();
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->index('name');
            $table->index('location');
        });

        // Backfill code for existing rows (e.g. when migrating from older schema)
        DB::table('warehouses')->orderBy('id')->get()->each(function ($row, $index) {
            DB::table('warehouses')->where('id', $row->id)->update([
                'code' => 'WH' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
