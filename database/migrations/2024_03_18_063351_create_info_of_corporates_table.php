<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfoOfCorporatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info_of_corporates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subscriber_id');
            $table->float('co_codes_used_percent');
            $table->string('co_code_string', 255)->nullable();
            $table->string('co_admin_name', 255)->nullable();
            $table->string('co_admin_email', 255)->nullable();
            $table->string('co_admin_phone', 255)->nullable();
            $table->string('co_category', 255)->nullable();
            $table->string('co_paid_codes_expired', 255)->nullable();
            $table->string('co_paid_codes_not_expired', 255)->nullable();//expired,unexpired
            $table->string('co_group_invoice_status', 255)->nullable();//unpaid, paid
            $table->integer('co_invoice_created_not_paid_number')->nullable();
            $table->float('co_invoice_created_not_paid_amount');
            $table->dateTime('co_invoice_created_not_paid_date')->nullable();
            $table->dateTime('group_codesexpire_datetime')->nullable();
            
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
        Schema::dropIfExists('info_of_corporates');
    }
}
