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
            // Subtotal before discount and tax
            $table->decimal('subtotal', 12, 2)->default(0)->after('total');
            
            // Discount fields
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('subtotal');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('discount_percentage');
            
            // Tax fields
            $table->decimal('tax_percentage', 5, 2)->default(0)->after('discount_amount');
            $table->decimal('tax_amount', 12, 2)->default(0)->after('tax_percentage');
            
            // Payment method
            $table->string('payment_method')->default('cash')->after('status');
            
            // Customer information (optional)
            $table->string('customer_name')->nullable()->after('payment_method');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->string('customer_email')->nullable()->after('customer_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'discount_percentage',
                'discount_amount',
                'tax_percentage',
                'tax_amount',
                'payment_method',
                'customer_name',
                'customer_phone',
                'customer_email',
            ]);
        });
    }
};
