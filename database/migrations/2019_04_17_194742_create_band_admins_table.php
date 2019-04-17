<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBandAdminsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('band_admins', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->decimal('payment', 6, 2);
			$table->json('data');
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
		Schema::dropIfExists('band_admins');
	}
}
