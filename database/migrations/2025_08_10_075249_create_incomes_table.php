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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('income_id');
            $table->string('number', 50)->default('');
            $table->date('date');
            $table->dateTime('last_change_date');
            $table->string('supplier_article', 50);
            $table->string('tech_size', 50);
            $table->bigInteger('barcode');
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->date('date_close');
            $table->string('warehouse_name', 100);
            $table->bigInteger('nm_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
