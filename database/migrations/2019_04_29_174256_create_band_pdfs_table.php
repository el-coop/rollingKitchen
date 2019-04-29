<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBandPdfsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('band_pdfs', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('file');
			$table->bigInteger('band_id')->unsigned();
			$table->timestamps();

			$table->foreign('band_id')
				->references('id')->on('bands')
				->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('band_pdfs');
	}
}
