<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblInterviewTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_interview', function (Blueprint $table) {
            $table->id('interview_id'); // Auto-increment primary key
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('user_id');
            $table->string('room_id', 50); // No foreign key constraint
            $table->dateTime('date_time');
            $table->enum('mode', ['Face-to-face', 'Online']);
            $table->decimal('scores', 5, 2)->nullable();
            $table->enum('status', ['Scheduled', 'Cancelled', 'Completed'])->default('Scheduled');
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Enables deleted_at for soft deletes

            // Foreign Key Constraints
            $table->foreign('applicant_id')->references('applicant_id')->on('applicants')->onDelete('cascade');
            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_interview');
    }
}
