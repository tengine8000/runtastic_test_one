<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGPSPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_points', function (Blueprint $table) {
            $table->id();
            $table->decimal('latitude', 15, 13);
            $table->decimal('longitude', 16, 13);
            $table->foreignId('trace_id');
            $table->timestamps();

            $table->index('latitude', 'latitude');
            $table->index('longitude', 'longitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gps_points');
    }
}
