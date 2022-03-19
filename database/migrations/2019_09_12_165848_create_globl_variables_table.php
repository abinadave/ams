<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGloblVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_variables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('physical_report_header1')->default('For which ARTEMIO B. CANEJA, REGIONAL DIRECTOR, DILG RO VIII is accountable, having assumed such accountability on August 10, 2016');
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
        Schema::dropIfExists('globl_variables');
    }
}
