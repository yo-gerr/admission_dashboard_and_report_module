<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); // Auto Increment
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('extension_name', 50)->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->unsignedBigInteger('role_id');
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth', 255)->nullable();
            $table->enum('sex', ['male', 'female', 'other'])->nullable();
            $table->integer('age')->nullable();
            $table->string('citizenship', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('blood_type', 255)->nullable();
            $table->enum('civil_status', ['Married', 'Single', 'Divorced', 'Widowed'])->nullable();
            $table->string('religion', 255)->nullable();
            $table->string('birth_order', 255)->nullable();
            $table->string('no_of_siblings', 255)->nullable();
            $table->timestamps();
            $table->datetime('last_login')->nullable();
            $table->string('profile_picture', 255)->nullable();
            $table->boolean('email_verified')->default(false);

            // Foreign key constraint
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
