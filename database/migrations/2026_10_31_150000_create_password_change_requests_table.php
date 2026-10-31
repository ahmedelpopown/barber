<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_change_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('account_type', 20);
            $table->unsignedBigInteger('account_id');
            $table->foreignId('requested_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('requested_by_name');
            $table->string('requested_by_email');
            $table->char('token_hash', 64)->unique();
            $table->string('status', 20)->default('pending');
            $table->timestamp('expires_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['account_type', 'account_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_change_requests');
    }
};
