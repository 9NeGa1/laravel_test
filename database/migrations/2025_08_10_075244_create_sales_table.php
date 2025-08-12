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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('g_number', 50);
            $table->date('date');
            $table->dateTime('last_change_date');
            $table->string('supplier_article', 50);
            $table->string('tech_size', 50);
            $table->bigInteger('barcode');
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount_percent', 5, 2);
            $table->boolean('is_supply');
            $table->boolean('is_realization');
            $table->decimal('promo_code_discount', 10, 2)->nullable();
            $table->string('warehouse_name', 100);
            $table->string('country_name', 50);
            $table->string('oblast_okrug_name', 100);
            $table->string('region_name', 100);
            $table->bigInteger('income_id');
            $table->string('sale_id', 50);
            $table->bigInteger('odid')->nullable();
            $table->decimal('spp', 5, 2);
            $table->decimal('for_pay', 10, 2);
            $table->decimal('finished_price', 10, 2);
            $table->decimal('price_with_disc', 10, 2);
            $table->bigInteger('nm_id');
            $table->string('subject', 50);
            $table->string('category', 50);
            $table->string('brand', 50);
            $table->boolean('is_storno')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
