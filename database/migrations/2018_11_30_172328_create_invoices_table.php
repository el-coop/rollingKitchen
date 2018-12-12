<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('invoices', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('application_id')->unsigned()->nullable();
			$table->string('prefix');
			$table->integer('number');
			$table->integer('amount')->default(0);
			$table->integer('tax');

			$table->timestamps();

			$table->foreign('application_id')
				->references('id')->on('applications');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('invoices');
	}
}
