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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->mediumText('name');
            $table->longText('description');
            $table->text('code');
            $table->bigInteger('price_sale');
            $table->bigInteger('price_discount')->default(0);
            $table->mediumText('preparation_time');
            $table->foreignId('status_id')->constrained('statuses');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('commerce_id')->constrained('commerces');
            $table->bigInteger('product_bank_id')->nullable();
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
        Schema::dropIfExists('products');
    }
}
