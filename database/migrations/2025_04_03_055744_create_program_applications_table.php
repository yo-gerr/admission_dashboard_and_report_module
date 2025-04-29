<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('program_applications', function (Blueprint $table) {
            $table->id('application_id');
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->string('first_choice', 255);
            $table->text('first_choice_reason')->nullable();
            $table->string('second_choice', 255)->nullable();
            $table->text('second_choice_reason')->nullable();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('applicant_id')->references('applicant_id')->on('applicants')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('academic_year_id')->on('academic_years')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('program_applications');
    }
}
