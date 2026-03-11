<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('purchases')) {
        Schema::create('purchases', function (Blueprint $table) 
        {
            $table->engine = 'InnoDB';   
            $table->bigIncrements('id');  
            $table->unsignedBigInteger('supplier_id')->nullable();           
            $table->string('invoice_no', 100)->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('total_amount', 10)->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->enum('payment_status', ['paid', 'partial', 'unpaid']);
            $table->unsignedInteger('company_id')->default(0);
            $table->unsignedInteger('office_id')->default(0);
            $table->unsignedBigInteger('created_by')->nullable(); //->index('purchase_entries_created_by_foreign');
            $table->timestamp('created_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable(); //->index('purchase_entries_updated_by_foreign');
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('deleted_by')->nullable(); //->index('purchase_entries_deleted_by_foreign');
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
        Schema::dropIfExists('purchase');
    }
}
