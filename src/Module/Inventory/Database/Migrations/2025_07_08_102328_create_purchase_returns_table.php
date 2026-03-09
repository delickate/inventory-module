<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->engine = 'InnoDB';   
            $table->integer('id', true);
            $table->integer('purchase_id');
            $table->integer('supplier_id')->nullable();
            $table->date('return_date');
            $table->decimal('total_amount', 10)->nullable()->default(0);
            $table->unsignedInteger('company_id')->default(0);
            $table->unsignedInteger('office_id')->default(0);
            $table->text('reason')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('purchase_returns');
    }
}
