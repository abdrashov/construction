<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices');

            $table->string('name', 255);
            $table->unsignedInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');

            $table->integer('count')->default(0)->unsigned();
            $table->integer('price')->default(0)->unsigned();

            $table->string('measurement')->nullable();

            $table->unsignedInteger('measurement_id');
            $table->foreign('measurement_id')->references('id')->on('measurements');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
}
