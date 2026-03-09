<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReturnItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';   
            $table->integer('id', true);
            $table->integer('purchase_return_id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10);
            $table->decimal('subtotal', 10);
            $table->unsignedInteger('company_id')->default(0);
            $table->unsignedInteger('office_id')->default(0);
            $table->timestamp('created_at')->nullable(); //->useCurrent();
            $table->timestamp('updated_at')->nullable(); //->useCurrentOnUpdate()->nullable()->useCurrent();
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
        Schema::dropIfExists('purchase_return_items');
    }
}
