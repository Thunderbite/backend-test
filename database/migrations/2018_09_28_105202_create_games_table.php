<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('campaign_id');
            $table->string('account'); //username of the user who played the game
            $table->unsignedInteger('prizeId')->nullable(); //id of the won prize
            $table->dateTime('revealed_at')->nullable(); //timestamp in campaign's timezone
            // when the game has been played - it can be different than created_at
            $table->timestamps();

            $table->index('id', 'default_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
};
