<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('expense_category_id');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories');

            $table->unsignedInteger('organization_id')->nullable();
            $table->foreign('organization_id')->references('id')->on('organizations');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            
            $table->string('name');
            $table->text('description')->nullable();
            $table->bigInteger('price')->nullable()->unsigned();
            $table->bigInteger('paid')->nullable()->unsigned();
            $table->date('date');

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
        Schema::dropIfExists('expenses');
    }
}
