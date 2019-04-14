<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBandMemberPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('band_member_photos', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('band_member_id')->unsigned();
			$table->string('file');
			$table->timestamps();
	
			$table->foreign('band_member_id')
				->references('id')->on('band_members')
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
        Schema::dropIfExists('band_member_photos');
    }
}
