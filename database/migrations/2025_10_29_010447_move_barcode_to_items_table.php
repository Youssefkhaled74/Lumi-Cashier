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
        // Add barcode to items table (nullable initially)
        Schema::table('items', function (Blueprint $table) {
            $table->string('barcode')->nullable()->unique()->after('sku');
        });

        // For SQLite, we need to drop the unique index first
        Schema::table('item_units', function (Blueprint $table) {
            $table->dropUnique(['barcode']);
        });

        // Then drop the column
        Schema::table('item_units', function (Blueprint $table) {
            $table->dropColumn('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add barcode back to item_units
        Schema::table('item_units', function (Blueprint $table) {
            $table->string('barcode')->unique()->after('item_id');
        });

        // Remove barcode from items
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('barcode');
        });
    }
};
