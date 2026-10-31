<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table): void {
            $table->foreignId('inventory_item_id')
                ->nullable()
                ->after('created_by_employee_id')
                ->constrained('inventory_items')
                ->restrictOnDelete();

            $table->unsignedInteger('inventory_quantity')
                ->nullable()
                ->after('inventory_item_id');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table): void {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn(['inventory_item_id', 'inventory_quantity']);
        });
    }
};
