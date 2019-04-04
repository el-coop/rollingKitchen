<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBandSongsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('band_songs', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('band_id')->unsigned();
			$table->string('title');
			$table->string('composer');
			$table->boolean('owned');
			$table->boolean('protected');
			
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
	public function down() {
		Schema::dropIfExists('band_songs');
	}
}
