<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatisticsForPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistics_for_pages', function (Blueprint $table) {
            $table->id();
            $table->string('page_name');
            $table->string('wording_day',15);
            $table->string('wording_month',15);
            $table->integer('day_connexion');
            $table->integer('month_connexion');
            $table->integer('year_connexion');
            $table->bigInteger('number_of_visits_by_date')->default(0);
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
        Schema::dropIfExists('statistics_for_pages');
    }
}
