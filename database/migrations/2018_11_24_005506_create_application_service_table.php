<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationServiceTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('application_service', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('application_id')->unsigned();
			$table->integer('service_id')->unsigned();
			$table->integer('quantity')->unsigned();
			$table->timestamps();
			
			$table->foreign('application_id')
				->references('id')->on('applications')
				->onDelete('cascade');
			$table->foreign('service_id')
				->references('id')->on('services')
				->onDelete('cascade');
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('application_service');
	}
}
