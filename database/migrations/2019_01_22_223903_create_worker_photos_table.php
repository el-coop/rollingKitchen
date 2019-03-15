<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkerPhotosTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('worker_photos', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('worker_id')->unsigned();
			$table->string('file');
			$table->timestamps();
			
			$table->foreign('worker_id')
				->references('id')->on('workers')
				->onDelete('cascade');
			
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('worker_photos');
	}
}
