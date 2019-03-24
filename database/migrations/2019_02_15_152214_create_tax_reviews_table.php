<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxReviewsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('tax_reviews', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('worker_id')->unsigned();
			$table->string('name');
			$table->string('file');
			$table->timestamps();
			
			
			$table->foreign('worker_id')
				->references('id')->on('workers')
				->onDelete('cascade');
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('tax_reviews');
	}
}
