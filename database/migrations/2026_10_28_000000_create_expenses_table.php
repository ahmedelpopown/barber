<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('category', 40)->index();
            $table->decimal('amount', 12, 2);
            $table->date('expense_date')->index();
            $table->string('payment_method', 40)->default('cash');
            $table->string('vendor')->nullable();
            $table->string('receipt_reference', 120)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['expense_date', 'category']);
            $table->index(['expense_date', 'payment_method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
