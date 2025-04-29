<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcademicYearsTable extends Migration
{
    public function up()
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id('academic_year_id');
            $table->year('start_year');
            $table->year('end_year');
            $table->enum('status', ['Active', 'Inactive'])->default('Inactive');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('academic_years');
    }
}
