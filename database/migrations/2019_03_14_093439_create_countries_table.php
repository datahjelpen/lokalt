<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET SESSION sql_require_primary_key=0');

        Schema::create('countries', function (Blueprint $table) {
            $table->smallIncrements('id')->primary();
            $table->string('name_short_local')->nullable()->default(null);
            $table->string('name_short_international');
            $table->string('name_official_local')->nullable()->default(null);
            $table->string('name_official_international')->nullable()->default(null);
            $table->string('code_iso')->unique();
            $table->string('code_call');
            $table->boolean('eu_member')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
