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
        Schema::table('order_items', function (Blueprint $table) {
            // Per-item discount
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('price');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('discount_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'discount_percentage',
                'discount_amount',
            ]);
        });
    }
};
