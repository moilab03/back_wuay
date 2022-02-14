<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommercesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerces', function (Blueprint $table) {
            $table->id();
            $table->longText('name');
            $table->longText('nit');
            $table->longText('contact');
            $table->text('email');
            $table->text('address');
            $table->mediumInteger('phone');
            $table->longText('web')->nullable();
            $table->mediumText('latitude');
            $table->mediumText('longitude');
            $table->longText('attention_schedule');
            $table->longText('security_code');

            $table->mediumInteger('quantity_table')->default(0);

            $table->foreignId('city_id')
                ->constrained('cities');

            $table->foreignId('status_id')
                ->constrained('statuses');

            $table->foreignId('user_id')
                ->constrained('users');
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
        Schema::dropIfExists('commerces');
    }
}
