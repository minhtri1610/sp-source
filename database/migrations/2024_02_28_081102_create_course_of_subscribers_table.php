<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseOfSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_of_subscribers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subscriber_id');
            $table->text('cs_course_name')->nullable();
            $table->boolean('cs_quiz_taken')->default(false);
            $table->boolean('cs_quiz_passed')->default(false);
            $table->boolean('cs_quiz_paid')->default(false);
            $table->tinyInteger('cs_quiz_expiring')->nullable();
            $table->date('cs_quiz_date')->nullable();
            $table->tinyInteger('cs_quiz_failed_attempts')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_of_subscribers');
    }
}
