<?php

use App\Models\ItemCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->tinyInteger('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        ItemCategory::create([
            'name' => 'Другие',
            'sort' => 1
        ]);
        ItemCategory::create([
            'name' => 'Бетоны',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_categories');
    }
}
