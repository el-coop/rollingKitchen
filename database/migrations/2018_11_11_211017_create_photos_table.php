<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotosTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('photos', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('kitchen_id')->unsigned();
			$table->string('file');
			$table->timestamps();
			
			$table->foreign('kitchen_id')
				->references('id')->on('kitchens')
				->onDelete('cascade');
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('photos');
	}
}
