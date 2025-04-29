<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegesTable extends Migration
{
    public function up()
    {
        Schema::create('colleges', function (Blueprint $table) {
            $table->id('college_id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('colleges');
    }
}
