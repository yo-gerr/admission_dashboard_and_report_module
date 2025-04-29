<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramsTable extends Migration
{
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id('program_id');
            $table->string('program_code', 50);
            $table->string('program_name', 255);
            $table->unsignedBigInteger('college_id');
            $table->text('description')->nullable();
            $table->integer('duration_years');
            $table->integer('max_students');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->date('application_starting');
            $table->date('application_end');
            $table->timestamps();

            $table->foreign('college_id')->references('college_id')->on('colleges')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('programs');
    }
}
