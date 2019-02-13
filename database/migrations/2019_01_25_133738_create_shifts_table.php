<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('shifts', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('workplace_id')->unsigned();
			$table->date('date');
			$table->integer('hours');
			$table->boolean('closed')->default(false);
			$table->timestamps();
			
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
		Schema::dropIfExists('shifts');
	}
}
