<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('expenses', 'receipt_images')) {
            Schema::table('expenses', function (Blueprint $table): void {
                $table->json('receipt_images')->nullable()->after('notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('expenses', 'receipt_images')) {
            Schema::table('expenses', function (Blueprint $table): void {
                $table->dropColumn('receipt_images');
            });
        }
    }
};
