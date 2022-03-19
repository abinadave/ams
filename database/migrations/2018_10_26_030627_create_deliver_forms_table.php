<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliverFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliver_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('po_number');
            $table->integer('charge_invoice_no')->nullable();
            $table->date('date_of_invoice')->nullable();
            $table->integer('apr_id')->nullable();
            $table->date('date_of_apr')->nullable();
            $table->date('date_of_delivery')->nullable();
            $table->date('date_received')->nullable();
            $table->integer('iar_no')->nullable();
            $table->string('purpose', 255);
            $table->integer('transact_by');
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
        Schema::dropIfExists('deliver_forms');
    }
}
