<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('inventory')) {
        Schema::create('inventory', function (Blueprint $table) {
            $table->engine = 'InnoDB';   
                $table->id(); 
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('warehouse_id')->nullable();
                //$table->foreign('product_id')->default(0); //->references('id')->on('products')->onDelete('cascade');
                //$table->foreign('warehouse_id')->default(0); //->references('id')->on('warehouses')->onDelete('cascade');
                $table->integer('quantity')->default(0);
                $table->unsignedInteger('company_id')->default(0);
                $table->unsignedInteger('office_id')->default(0);
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
        Schema::dropIfExists('inventory');
    }
}
