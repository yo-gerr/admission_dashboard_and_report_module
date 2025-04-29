<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionDecisionsTable extends Migration
{
    public function up()
    {
        Schema::create('admission_decisions', function (Blueprint $table) {
            $table->id('decision_id');
            $table->unsignedBigInteger('result_id');
            $table->enum('decision_type', ['Accepted', 'Rejected', 'Conditional']);
            $table->text('decision_letter_content')->nullable();
            $table->date('decision_date');
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('result_id')->references('result_id')->on('admission_results')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admission_decisions');
    }
}
