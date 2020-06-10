<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->integer('qty')->nullable();
            $table->decimal('total', 8, 3)->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->bigInteger('customer_id')->nullable()->unsigned();
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('customer_email')->nullable();
            $table->string('customer_firstname')->nullable();
            $table->string('customer_lastname')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carts');
    }
}
