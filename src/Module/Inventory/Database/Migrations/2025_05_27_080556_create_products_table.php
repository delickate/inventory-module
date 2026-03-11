<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('products')) {
        Schema::create('products', function (Blueprint $table) {
            $table->engine = 'InnoDB';   
            $table->id();
            $table->string('name')->collation('utf8mb3_bin');
            $table->string('sku', 100)->nullable()->collation('utf8mb3_bin')->index();
            $table->string('barcode', 100)->nullable()->collation('utf8mb3_bin');
          
            // $table->unsignedBigInteger('category_id')->index();
            // $table->unsignedBigInteger('unit_id')->index();

            $table->integer('minimum_quantity')->nullable()->default(0);
            $table->integer('reorder_quantity')->nullable()->default(0);
          
            $table->decimal('cost_price', 10, 2)->nullable()->default(0.00);
            $table->decimal('sale_price', 10, 2)->nullable()->default(0.00);
            $table->text('description')->nullable()->collation('utf8mb3_bin');
            $table->string('image')->nullable()->collation('utf8mb3_bin');
            $table->tinyInteger('status')->nullable()->default(1);

            $table->foreignId('category_id')->nullable(); //->constrained('categories')->onDelete('cascade');
            $table->foreignId('unit_id')->nullable(); //->constrained('units')->onDelete('cascade');

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
        Schema::dropIfExists('products');
    }
}
