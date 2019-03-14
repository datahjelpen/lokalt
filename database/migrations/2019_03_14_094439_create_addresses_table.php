<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('street_name');
            $table->string('street_number');
            $table->string('postal_code');
            $table->string('postal_city');
            $table->string('province');
            $table->decimal('latitude', 8, 6); // http://mysql.rjweb.org/doc.php/latlng
            $table->decimal('longitude', 9, 6);
            $table->uuid('country_id');
            $table->foreign('country_id')->references('id')->on('countries');
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
        Schema::dropIfExists('addresses');
    }
}
