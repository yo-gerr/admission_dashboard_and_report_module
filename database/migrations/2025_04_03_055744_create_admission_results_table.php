<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionResultsTable extends Migration
{
    public function up()
    {
        Schema::create('admission_results', function (Blueprint $table) {
            $table->id('result_id');
            $table->unsignedBigInteger('applicant_id');
            $table->enum('admission_status', ['Pending', 'Accepted', 'Rejected'])->default('Pending');
            $table->float('test_score')->nullable();
            $table->text('interview_result')->nullable();
            $table->enum('document_verification_status', ['Pending', 'Verified', 'Rejected'])->default('Pending');
            $table->string('decision_letter', 255)->nullable();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('applicant_id')->references('applicant_id')->on('applicants')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admission_results');
    }
}
