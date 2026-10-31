<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_usages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inventory_item_id')
                ->constrained('inventory_items')
                ->restrictOnDelete();
            $table->foreignId('started_by_employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();
            $table->foreignId('finished_by_employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();
            $table->string('status', 20)->default('in_use')->index();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['inventory_item_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_usages');
    }
};
