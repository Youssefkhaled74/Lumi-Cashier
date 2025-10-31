<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['day_id']);
            
            // Re-add it with cascade delete
            $table->foreign('day_id')
                  ->references('id')
                  ->on('days')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the cascade constraint
            $table->dropForeign(['day_id']);
            
            // Restore the original set null behavior
            $table->foreign('day_id')
                  ->references('id')
                  ->on('days')
                  ->onDelete('set null');
        });
    }
};
