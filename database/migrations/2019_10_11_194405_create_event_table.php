<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('title');
            $table->string('description');
            $table->string('event_id');
            $table->date('date');
            $table->string('event_time');
            $table->string('address');
            $table->string('price');
            $table->string('enroll_date');
            $table->string('final_price');
            $table->string('picture_link');
            $table->string('year');
            $table->boolean('publish');
            $table->string('event_url');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event');
    }
}
