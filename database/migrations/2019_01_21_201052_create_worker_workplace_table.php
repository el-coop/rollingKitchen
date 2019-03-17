<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkerWorkplaceTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('worker_workplace', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('worker_id')->unsigned();
			$table->integer('workplace_id')->unsigned();

			$table->timestamps();
			
			
			$table->foreign('worker_id')
				->references('id')->on('workers')
				->onDelete('cascade');
			$table->foreign('workplace_id')
				->references('id')->on('workplaces')
				->onDelete('cascade');
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('worker_workplace');
	}
}
