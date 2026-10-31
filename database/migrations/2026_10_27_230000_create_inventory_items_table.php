<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('sku', 64)->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('unit', 40)->default('قطعة');
            $table->decimal('unit_price', 12, 2)->default(0);
            // Includes the unit currently in use, if any.
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('minimum_quantity')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
