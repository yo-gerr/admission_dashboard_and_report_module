<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id('applicant_id'); // Auto Increment Primary Key
            $table->unsignedBigInteger('academic_year_id'); // Foreign key linking to academic years
            $table->unsignedBigInteger('appform_id'); // Application Form ID (not auto-increment)
            $table->enum('status', ['Pending', 'Received', 'Under Review', 'Approved', 'For Interview', 'For Test', 'Accepted', 'Not Accepted'])->default('Pending');
            $table->string('last_name', 255);
            $table->string('first_name', 255);
            $table->string('middle_name', 255)->nullable();
            $table->string('extension_name', 50)->nullable();
            $table->date('date_of_birth');
            $table->string('place_of_birth', 255)->nullable();
            $table->enum('sex', ['Male', 'Female']);
            $table->integer('age');
            $table->string('blood_type', 5)->nullable();
            $table->string('citizenship', 255);
            $table->enum('civil_status', ['Single', 'Married']);
            $table->string('religion', 255)->nullable();
            $table->integer('birth_order')->nullable();
            $table->integer('no_of_siblings')->nullable();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('academic_year_id')->references('academic_year_id')->on('academic_years')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('applicants');
    }
};
