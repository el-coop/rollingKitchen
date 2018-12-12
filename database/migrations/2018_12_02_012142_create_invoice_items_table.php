<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceItemsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('invoice_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('invoice_id')->unsigned();
			$table->integer('service_id')->unsigned()->nullable();
			$table->integer('quantity')->unsigned();
			$table->integer('tax')->default(0)->unsigned();
			$table->decimal('unit_price', 10, 2)->unsigned();
			$table->string('name');
			
			$table->timestamps();
			
			$table->foreign('invoice_id')
				->references('id')->on('invoices')
				->onDelete('cascade');
			$table->foreign('service_id')
				->references('id')->on('services');
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('invoice_items');
	}
}
