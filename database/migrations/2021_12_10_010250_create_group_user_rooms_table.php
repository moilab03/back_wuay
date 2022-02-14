<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupUserRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_user_rooms', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('receiver_group_id');
            $table->unsignedBigInteger('sender_group_id');
            $table->foreign('receiver_group_id')
                ->references('id')
                ->on('group_users');

            $table->foreign('sender_group_id')
                ->references('id')
                ->on('group_users');

            $table->foreignId('status_id')
                ->constrained('statuses');

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
        Schema::dropIfExists('group_user_rooms');
    }
}
