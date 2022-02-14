<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->bigInteger('phone')
                ->nullable();

            $table->string('email')
                ->nullable()
                ->unique();

            $table->string('password')
                ->nullable();

            $table->foreignId('status_id')
                ->constrained('statuses');

            $table->foreignId('rol_id')
                ->constrained(
                    'rols');

            $table->bigInteger('current_commerce_id')
                ->nullable();
            $table->boolean('terms_and_conditions')->default(true);

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
