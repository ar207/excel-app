<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPackageCombinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_package_combinations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_table_id')->nullable();
            $table->bigInteger('package_table_id')->nullable();
            $table->string('ndc')->nullable();
            $table->string('ndc_match')->nullable();
            $table->mediumText('name')->nullable();
            $table->string('strength')->nullable();
            $table->string('unit')->nullable();
            $table->mediumText('labeler_name')->nullable();
            $table->mediumText('brand_name')->nullable();
            $table->string('dosage_form')->nullable();
            $table->string('count')->nullable();
            $table->string('meta_data')->nullable();
            $table->string('meta_description')->nullable();
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
        Schema::dropIfExists('product_package_combinations');
    }
}
