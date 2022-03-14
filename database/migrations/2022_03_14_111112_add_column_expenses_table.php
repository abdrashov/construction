<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->tinyInteger('sort')->default(0)->after('id');
        });
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedInteger('item_category_id')->after('id')->default(1);
            $table->foreign('item_category_id')->references('id')->on('item_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['item_category_id']);
            $table->dropColumn('item_category_id');
        });
    }
}
