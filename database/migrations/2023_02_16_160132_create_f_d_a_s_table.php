<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFDASTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_d_a_s', function (Blueprint $table) {
            $table->id();
            $table->string('ndc')->nullable();
            $table->string('ndc_match')->nullable();
            $table->string('name')->nullable();
            $table->string('strength')->nullable();
            $table->string('form')->nullable();
            $table->string('count')->nullable();
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
        Schema::dropIfExists('f_d_a_s');
    }
}
