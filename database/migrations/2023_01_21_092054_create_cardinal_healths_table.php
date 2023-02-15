<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardinalHealthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cardinal_healths', function (Blueprint $table) {
            $table->id();
            $table->mediumText('cin_ndc_upc')->nullable();
            $table->mediumText('cin_ndc_upc1')->nullable();
            $table->mediumText('trade_name_mfr')->nullable();
            $table->string('trade_name_mfr2')->nullable();
            $table->mediumText('strength')->nullable();
            $table->string('from')->nullable();
            $table->string('size')->nullable();
            $table->string('type')->nullable();
            $table->string('net_cost')->nullable();
            $table->string('invoice_cost')->nullable();
            $table->string('cardinal')->nullable();
            $table->integer('file_category_id')->nullable();
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
        Schema::dropIfExists('cardinal_healths');
    }
}
