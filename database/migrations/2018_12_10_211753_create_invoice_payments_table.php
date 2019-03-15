<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicePaymentsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('invoice_payments', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->decimal('amount');
			$table->date('date');
			$table->integer('invoice_id')->unsigned();

			$table->timestamps();

			$table->foreign('invoice_id')
				->references('id')->on('invoices')
				->onDelete('cascade');;
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('invoice_payments');
	}
}
