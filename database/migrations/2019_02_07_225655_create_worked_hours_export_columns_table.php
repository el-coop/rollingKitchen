<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkedHoursExportColumnsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('worked_hours_export_columns', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->integer('order');
			$table->string('column');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('worked_hours_export_columns');
	}
}
