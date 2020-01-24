<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('eventbrite_id');
            $table->string('lms_id');
            $table->string('lms_group_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('cell_phone');
            $table->string('address1');
            $table->string('address2');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('country_code');
            $table->string('zip');
            $table->string('Gender');
            $table->boolean('l1_p1_exam_passed');
            $table->string('l1_p1_exam_score');
            $table->boolean('l1_p2_exam_passed');
            $table->string('email');
            $table->string('lms_email');
            
            // eventbrite_id ,lms_id ,lms_group_id, first_name , last_name ,cell_phone , address1 (opt),  email ,lms_email , company( opt ) , address1(opt) , address2(opt) , city(opt) , state (opt), country , country_code , zip , phone , Gender , age ,l1-p1-exam-passed ,l1-p2-exam-passed

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
