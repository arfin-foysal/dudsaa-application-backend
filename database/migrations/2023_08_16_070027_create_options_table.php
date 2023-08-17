<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //poll_options
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("polls_id"); 
            $table->string("option"); 
            // Poll_id = 1
            // 1. Option A(4) 
            // 2. Option B(3) 
            // 3. Option C(15)
            $table->bigInteger("votes")->default(0);
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
        Schema::dropIfExists('options');
    }
}
