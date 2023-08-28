<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAditionalField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
                   $table->string('company_name')->nullable();
                   $table->string('job_nature')->nullable();
                   $table->string('location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
                
                $table->dropColumn('company_name');
                $table->dropColumn('job_nature');
                $table->dropColumn('location');
           
            
        });
    }
}
