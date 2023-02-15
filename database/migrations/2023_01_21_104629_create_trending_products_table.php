<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrendingProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trending_products', function (Blueprint $table) {
            $table->id();
            $table->string('ndc')->nullable();
            $table->string('product_name')->nullable();
            $table->string('strength')->nullable();
            $table->string('package_size')->nullable();
            $table->string('from')->nullable();
            $table->string('mfr')->nullable();
            $table->string('type')->nullable();
            $table->string('low_sold_price')->nullable();
            $table->string('avg_sold_price')->nullable();
            $table->string('high_sold_price')->nullable();
            $table->string('best_price_today')->nullable();
            $table->string('trxade')->nullable();
            $table->string('file_category_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trending_products');
    }
}
