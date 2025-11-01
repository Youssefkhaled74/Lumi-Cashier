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
        Schema::table('shop_settings', function (Blueprint $table) {
            $table->boolean('tax_enabled')->default(true)->after('address_en');
            $table->decimal('tax_percentage', 5, 2)->default(15)->after('tax_enabled');
            $table->string('tax_label')->default('VAT')->after('tax_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_settings', function (Blueprint $table) {
            $table->dropColumn(['tax_enabled', 'tax_percentage', 'tax_label']);
        });
    }
};
