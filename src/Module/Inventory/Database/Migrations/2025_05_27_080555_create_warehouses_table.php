<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('warehouses')) {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->engine = 'InnoDB';   
            $table->id();
            $table->string('name')->collation('utf8mb3_bin');
            $table->string('location')->nullable()->collation('utf8mb3_bin');
            $table->unsignedInteger('company_id')->default(0);
            $table->unsignedInteger('office_id')->default(0);
            $table->softDeletes(); 
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
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
        Schema::dropIfExists('warehouses');
    }
}
