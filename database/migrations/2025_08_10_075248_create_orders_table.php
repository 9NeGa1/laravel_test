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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('g_number', 50);
            $table->dateTime('date');
            $table->dateTime('last_change_date');
            $table->string('supplier_article', 50);
            $table->string('tech_size', 50);
            $table->bigInteger('barcode');
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount_percent', 5, 2);
            $table->string('warehouse_name', 100);
            $table->string('oblast', 100);
            $table->bigInteger('income_id');
            $table->string('odid', 50);
            $table->bigInteger('nm_id');
            $table->string('subject', 50);
            $table->string('category', 50);
            $table->string('brand', 50);
            $table->boolean('is_cancel')->default(false);
            $table->dateTime('cancel_dt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
