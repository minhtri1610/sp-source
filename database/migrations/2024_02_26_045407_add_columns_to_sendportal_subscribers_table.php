<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToSendportalSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sendportal_subscribers', function (Blueprint $table) {
            $table->bigInteger('cs_source_id')->nullable();
            $table->string('cs_company_name', 255)->nullable();
            $table->string('cs_phone_number', 255)->nullable();
            $table->text('cs_short_email')->nullable();
            $table->text('cs_short_sms')->nullable();
            $table->boolean('cs_corporate_user')->default(false);
            $table->string('cs_corporate_code', 255)->nullable();
            $table->string('cs_source_web', 255)->nullable();
            $table->string('cs_user_name', 255)->nullable();
            $table->text('cs_course_name')->nullable();
            $table->boolean('cs_quiz_taken')->default(false);
            $table->boolean('cs_quiz_passed')->default(false);
            $table->boolean('cs_quiz_paid')->default(false);
            $table->tinyInteger('cs_quiz_expiring')->nullable();
            $table->date('cs_quiz_date')->nullable();
            $table->tinyInteger('cs_quiz_failed_attempts')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sendportal_subscribers', function (Blueprint $table) {
            $table->dropColumn('cs_source_id');
            $table->dropColumn('cs_company_name');
            $table->dropColumn('cs_phone_number');
            $table->dropColumn('cs_short_email');
            $table->dropColumn('cs_short_sms');
            $table->dropColumn('cs_corporate_user');
            $table->dropColumn('cs_corporate_code');
            $table->dropColumn('cs_source_web');
            $table->dropColumn('cs_user_name');
            $table->dropColumn('cs_course_name');
            $table->dropColumn('cs_quiz_taken');
            $table->dropColumn('cs_quiz_passed');
            $table->dropColumn('cs_quiz_paid');
            $table->dropColumn('cs_quiz_expiring');
            $table->dropColumn('cs_quiz_date');
            $table->dropColumn('cs_quiz_failed_attempts');
        });
    }
}