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
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('IC_no')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('status')->default('UNVERIFIED');
            $table->string('gender')->nullable();
            $table->longtext('IC_image')->default("images/default_profile_picture.png");
            $table->longtext('selfie')->nullable();
            $table->string('telephone')->nullable();
            $table->string('role');
            $table->string('fcm_token')->nullable();
            $table->longtext('home_address')->nullable();
            $table->string('remember_token')->nullable();
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
