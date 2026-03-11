<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('stock_movements')) {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->engine = 'InnoDB';   
            $table->id(); 
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();
         
            //$table->foreign('product_id')->references('id');->on('products')->onDelete('cascade');
            //$table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');

            $table->enum('type', ['IN', 'OUT', 'Adjustment']);
            $table->decimal('quantity', 10, 2);
            $table->text('reason')->nullable();
            $table->bigInteger('reference_id')->nullable();
            $table->unsignedInteger('company_id')->default(0);
            $table->unsignedInteger('office_id')->default(0);
            $table->bigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
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
        Schema::dropIfExists('stock_movements');
    }
}
