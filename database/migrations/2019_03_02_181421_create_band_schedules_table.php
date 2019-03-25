<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBandSchedulesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('band_schedules', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->timestamp('date_time');
			$table->bigInteger('stage_id')->unsigned();
			$table->bigInteger('band_id')->unsigned();
			$table->decimal('payment', 10, 2);
			$table->string('approved')->default('pending');
			
			$table->timestamps();
			
			$table->unique(['stage_id', 'date_time']);
			$table->unique(['band_id', 'date_time']);
			$table->foreign('band_id')
				->references('id')->on('bands')
				->onDelete('cascade');
			$table->foreign('stage_id')
				->references('id')->on('stages')
				->onDelete('cascade');
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('band_schedules');
	}
}
