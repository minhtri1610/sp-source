<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsFromCourseOfSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_of_subscribers', function (Blueprint $table) {
            $table->string('code_course', 255)->nullable();
            $table->boolean('sent_cheap_mail')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_of_subscribers', function (Blueprint $table) {
            $table->dropColumn('code_course');
            $table->dropColumn('sent_cheap_mail');
        });
    }
}
