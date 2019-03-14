<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaceOpenHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('place_open_hours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('place_id');
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
            $table->tinyInteger('weekday'); // 0 is monday, 6 is sunday
            $table->time('time_from')->nullable()->default(null);
            $table->time('time_to')->nullable()->default(null);
            $table->date('special_hours_date')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('place_open_hours');
    }
}
