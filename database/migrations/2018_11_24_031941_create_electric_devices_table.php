<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElectricDevicesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('electric_devices', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('application_id')->unsigned();
			$table->string('name');
			$table->integer('watts');
			$table->timestamps();
			
			$table->foreign('application_id')
				->references('id')->on('applications')
				->onDelete('cascade');
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('electric_devices');
	}
}
