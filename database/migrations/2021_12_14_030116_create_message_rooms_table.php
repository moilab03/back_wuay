<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_rooms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('group_user_room_id')
                ->nullable()
                ->constrained('group_user_rooms');

            $table->foreignId('group_sender_id')
                ->constrained('group_users');

            $table->foreignId('group_receiver_id')
                ->nullable()
                ->constrained('group_users');
            $table->longText('message');
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
        Schema::dropIfExists('message_rooms');
    }
}
