<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('categories')) 
        {
            Schema::create('categories', function (Blueprint $table) {
                $table->engine = 'InnoDB';   
                $table->id(); 
                $table->string('name', 191)->nullable()->index();
                $table->text('description')->nullable();
                $table->softDeletes(); 
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
