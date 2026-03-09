<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->engine = 'InnoDB';   
            $table->id(); // Auto-incrementing primary key (equivalent to your id field)
            $table->unsignedInteger('company_id')->default(0);
            $table->unsignedInteger('office_id')->default(0);
            $table->string('name', 255)->nullable()->collation('utf8mb4_bin');
            $table->string('email', 100)->nullable()->collation('utf8mb4_bin');
            $table->string('contact', 100)->nullable()->collation('utf8mb4_bin');
            $table->string('address', 100)->nullable()->collation('utf8mb4_bin');
            $table->foreignId('created_by')->nullable(); //->constrained('users')->onDelete('set null');
            $table->timestamp('created_at')->nullable();
            $table->foreignId('updated_by')->nullable(); //->constrained('users')->onDelete('set null');
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes(); // Creates deleted_at column for soft deletes
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}
