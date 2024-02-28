<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsFromSendportalSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sendportal_subscribers', function (Blueprint $table) {
            $table->dropColumn('cs_course_name');
            $table->dropColumn('cs_quiz_taken');
            $table->dropColumn('cs_quiz_passed');
            $table->dropColumn('cs_quiz_paid');
            $table->dropColumn('cs_quiz_expiring');
            $table->dropColumn('cs_quiz_date');
            $table->dropColumn('cs_quiz_failed_attempts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
