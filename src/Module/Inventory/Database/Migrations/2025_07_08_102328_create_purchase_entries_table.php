<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_entries', function (Blueprint $table) {
            $table->engine = 'InnoDB';   
            $table->bigIncrements('id');
           // $table->unsignedBigInteger('office_id')->default('1');
            $table->unsignedBigInteger('purchase_id')->nullable(); //->index('purchase_entries_supplier_id_foreign');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->decimal('unit_price', 10)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('total_amount', 10)->nullable();
            $table->unsignedInteger('company_id')->default(0);
            $table->unsignedInteger('office_id')->default(0);
            $table->boolean('is_paid')->default(false);
            $table->enum('status', ['Pending', 'Completed','Returned'])->default('Pending');
            $table->unsignedBigInteger('created_by')->nullable()->index('purchase_entries_created_by_foreign');
            $table->timestamp('created_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable()->index('purchase_entries_updated_by_foreign');
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('deleted_by')->nullable()->index('purchase_entries_deleted_by_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_entries');
    }
}
