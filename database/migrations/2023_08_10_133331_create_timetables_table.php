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
        Schema::create('timetables', function (Blueprint $table) {
            $table->tinyIncrements('id')->unsigned();
            $table->string('timetable_name');
            $table->string('shitf_name')->nullable();
            $table->time('checkin_time');
            $table->time('checkout_time');
            $table->string('late_minute', 100)->nullable();
            $table->string('early_leave', 100)->nullable();
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
        Schema::dropIfExists('timetables');
    }
};
