<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('organization_id');
            $table->foreign('organization_id')->references('id')->on('organizations');

            $table->string('name', 255);
            $table->boolean('status')->default(false);
            $table->date('date');
            $table->string('supplier', 255);
            $table->string('accepted', 255);
            
            $table->unsignedInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            
            $table->unsignedInteger('accepted_id');
            $table->foreign('accepted_id')->references('id')->on('accepteds');

            $table->string('file', 255)->nullable();

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
        Schema::dropIfExists('invoices');
    }
}
