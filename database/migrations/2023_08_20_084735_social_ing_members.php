<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SocialIngMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table -> string('facebook') -> nullable();
            $table -> string('twitter') -> nullable();
            $table -> string('instagram') -> nullable();
            $table -> string('linkedin') -> nullable();
         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table -> string('facebook') -> nullable();
            $table -> string('twitter') -> nullable();
            $table -> string('instagram') -> nullable();
            $table -> string('linkedin') -> nullable();
        });
    }
}
