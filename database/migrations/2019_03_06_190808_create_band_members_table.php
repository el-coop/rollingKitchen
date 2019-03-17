<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBandMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('band_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('band_id')->unsigned();
			$table->boolean('submitted')->default(false);
            $table->json('data');
            $table->timestamps();
			$table->foreign('band_id')
				->references('id')->on('bands')
				->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('band_members');
    }
}
