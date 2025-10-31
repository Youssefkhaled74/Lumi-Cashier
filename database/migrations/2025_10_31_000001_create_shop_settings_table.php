<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shop_settings', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name')->default('نظام لومي POS');
            $table->string('shop_name_en')->default('Lumi POS System');
            $table->string('logo_path')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('address_en')->nullable();
            $table->timestamps();
        });

        // إدراج القيم الافتراضية
        DB::table('shop_settings')->insert([
            'shop_name' => 'نظام لومي POS',
            'shop_name_en' => 'Lumi POS System',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_settings');
    }
};
