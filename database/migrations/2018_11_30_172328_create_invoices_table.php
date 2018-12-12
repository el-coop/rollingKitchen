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
			$table->integer('owner_id')->unsigned()->nullable();
			$table->string('owner_type');
			$table->string('prefix');
			$table->integer('number');
			$table->decimal('amount', 10, 2)->default(0);
			$table->integer('tax');

			$table->timestamps();
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
