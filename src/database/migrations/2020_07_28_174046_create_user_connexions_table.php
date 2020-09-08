<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserConnexionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

     /**
      * We can use this table to select Total number of connecting users by checking time_logout value for each row null mean user is login
      * by using same checking of time_logout we can get the state of each user , we can also get connexion_time for each user or 
      * AVG(connexion_time) of all users , this Helps us to know if users are unsterting to stay login on our application  
      */
    public function up()
    {

        // add index and primary keys

        Schema::create('user_connexions', function (Blueprint $table) {
            $table->bigIncrements('id');            
            //if the user logs in multiple times, we take the highest time value
            $table->integer('connexion_time')->default(0); //exp : 5 (5H)    
            $table->string('wording_day',15);
            $table->string('wording_month',15);
            $table->integer('day_connexion');
            $table->integer('month_connexion');
            $table->integer('year_connexion');           
            $table->time('time_login',0)->nullable();//we use time_login to count connexion_time
            $table->time('time_logout',0)->nullable();// null == state >> online , otherwise state >> offline
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('user_connexions');
    }
}