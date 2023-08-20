<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('education_informations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('member_id');
            $table->string('standard');
            $table->string('institution');
            $table->string('passing_year')->nullable();
            $table->string('result')->nullable();
            $table->enum('status', ['Completed', 'Ongoing', 'OnHold'])->default('Ongoing');
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('education_informations');
    }
}
