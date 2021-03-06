<?php

  use Illuminate\Support\Facades\Schema;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Database\Migrations\Migration;

  class CreatePointsTable extends Migration
  {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
      Schema::create('points', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('map_id')->unsigned();
        $table->string('lat');
        $table->string('lng');
        $table->string('name');
        $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
      Schema::dropIfExists('points');
    }
  }
