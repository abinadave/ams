<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ris_year');
            $table->integer('ris_month');
            $table->integer('ris_number');
            $table->string('division');
            $table->string('office')->default('DILG Regional Office 08');
            $table->string('resp_center_code')->nullable();
            $table->string('fund_cluster')->default('01');
            $table->string('requested_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('issued_by')->nullable();
            $table->string('received_by')->nullable();
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
        Schema::dropIfExists('request_forms');
    }
}
