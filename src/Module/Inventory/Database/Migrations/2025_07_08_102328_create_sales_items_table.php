<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         if (!Schema::hasTable('sale_items')) {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';   
            $table->integer('id', true);
            $table->integer('sale_id')->nullable(); //->index('customer_id');
            $table->integer('product_id')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10)->nullable()->default(0);
            $table->decimal('subtotal', 10)->nullable()->default(0);
            $table->unsignedInteger('company_id')->default(0);
            $table->unsignedInteger('office_id')->default(0);
            $table->integer('created_by')->nullable();
            $table->timestamp('created_at')->nullable(); //->useCurrent();
            $table->timestamp('updated_at')->nullable(); //->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
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
        Schema::dropIfExists('sales_items');
    }
}
