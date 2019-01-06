<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePdfsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('pdfs', function (Blueprint $table) {
			$table->increments('id');
			$table->string('file');
			$table->string('name');
			$table->integer('visibility');
			$table->boolean('default_send_invoice')->default(false);
			$table->boolean('default_resend_invoice')->default(false);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('pdfs');
	}
}
