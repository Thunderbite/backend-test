<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prizes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('campaign_id');
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('level'); //low, med, high
            $table->decimal('weight', 10, 2)->nullable(); // 0.01 - 99.99, determines the chance of winning
            $table->timestamp('startDate')->nullable(); //prize can be won from this date onwards
            $table->timestamp('endDate')->nullable(); //until this date
            $table->timestamps();

            $table->index(['title', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prizes');
    }
};
