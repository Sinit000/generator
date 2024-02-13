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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('checkin_time',10)->nullable();
            $table->string('checkout_time',10)->nullable();
            $table->string('checkin_late',50)->nullable();
            $table->string('checkout_early',50)->nullable();

           
            $table->string('attendance_status',50)->nullable();
          
            $table->string('date',50);
           
            $table->string('total_duration',20)->nullable();
            $table->string('attendance_worked',20)->nullable();

            $table->unsignedBigInteger('user_id');
            $table->string('status',20)->nullable();
            $table->string('status_hour',20)->nullable();
            $table->string('absent',20)->nullable();
            $table->string('break',10)->nullable();
            $table->string('leave_type',10)->nullable();
            $table->string('ot_level_one',10)->nullable();
        
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('attendances');
    }
};
