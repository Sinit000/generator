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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable();
            $table->string('email', 50)->nullable();
            $table->string('terminal_id')->nullable();
            $table->unsignedTinyInteger('role_id')->nullable();
            $table->unsignedTinyInteger('department_id')->nullable();
            $table->unsignedTinyInteger('timetable_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('session_id', 40)->nullable();
            $table->foreign('role_id')->nullable()->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('department_id')->nullable()->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('timetable_id')->nullable()->references('id')->on('timetables')->onDelete('cascade');
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
};
