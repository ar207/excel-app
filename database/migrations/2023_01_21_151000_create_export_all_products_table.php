<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportAllProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('export_all_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('file_category_id')->nullable();
            $table->string('name')->nullable();
            $table->string('vendor')->nullable();
            $table->string('ndc')->nullable();
            $table->string('price')->nullable();
            $table->string('wholesaler')->nullable();
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
        Schema::dropIfExists('export_all_products');
    }
}
