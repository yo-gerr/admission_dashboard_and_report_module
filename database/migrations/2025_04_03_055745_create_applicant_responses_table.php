<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantResponsesTable extends Migration
{
    public function up()
    {
        Schema::create('applicant_responses', function (Blueprint $table) {
            $table->id('response_id');
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('decision_id');
            $table->enum('response_status', ['Accepted', 'Declined']);
            $table->date('response_date');
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('applicant_id')->references('applicant_id')->on('applicants')->onDelete('cascade');
            $table->foreign('decision_id')->references('decision_id')->on('admission_decisions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('applicant_responses');
    }
}
