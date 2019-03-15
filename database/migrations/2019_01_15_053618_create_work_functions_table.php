<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkFunctionsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('work_functions', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->decimal('payment_per_hour_before_tax');
			$table->decimal('payment_per_hour_after_tax');
			$table->integer('workplace_id')->unsigned();
			$table->foreign('workplace_id')
				->references('id')->on('workplaces')
				->onDelete('cascade');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('work_functions');
	}
}
