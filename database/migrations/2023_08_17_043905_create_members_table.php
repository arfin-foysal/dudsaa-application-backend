<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('name');
            $table->string('email')->uniqid();
            $table->string('contact_no')->nullable();
            $table->string('alternative_contact_no')->nullable();
            $table->string('gender')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('bio')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('religion')->nullable();
            $table->string('marital_status')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('city_id')->nullable();
            // $table->bigInteger('division_id')->nullable();
            // $table->bigInteger('district_id')->nullable();
            // $table->bigInteger('city_id')->nullable();
            // $table->bigInteger('area_id')->nullable();
            $table->string('nid_no')->nullable();
            $table->string('birth_certificate_no')->nullable();
            $table->string('passport_no')->nullable();
            $table->string('image')->nullable();
            $table->dateTime('last_blood_donation_date')->nullable();
            $table->boolean('interested_to_donate')->default(true);
            $table->string('student_id_no')->nullable(); 
            $table->string('department')->nullable();
            $table->string('institute_id')->nullable();
            $table->string('batch_no')->nullable();
            $table->enum('status', ['Active', 'Pending', 'Suspended', 'On-Hold'])->default('Pending');
            $table->boolean('is_active')->default(0);
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
        Schema::dropIfExists('members');
    }
}
