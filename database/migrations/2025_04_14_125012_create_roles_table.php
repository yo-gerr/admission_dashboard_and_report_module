<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id'); // Auto Increment
            $table->string('role_name', 255);
            $table->enum('role_abbreviation', ['SA', 'AA', 'PH', 'FF']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
};
