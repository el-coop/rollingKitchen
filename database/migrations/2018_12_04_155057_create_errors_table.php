<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateErrorsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('errors', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('user_id')->unsigned()->nullable();
			$table->string('page');
			$table->integer('error_id')->unsigned();
			$table->string('error_type');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('errors');
	}
}
