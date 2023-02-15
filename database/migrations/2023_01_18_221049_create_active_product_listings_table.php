<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActiveProductListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('active_product_listings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('file_category_id')->nullable();
            $table->mediumText('desc_one')->nullable();
            $table->mediumText('vendor')->nullable();
            $table->mediumText('ndc')->nullable();
            $table->mediumText('list_price')->nullable();
            $table->mediumText('gpw')->nullable();
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
        Schema::dropIfExists('active_product_listings');
    }
}
